<?php

namespace App\Console\Commands;

use App\Models\JadwalSidang;
use App\Models\SyncLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncSippJadwalSidang extends Command
{
    protected $signature = 'sipp:sync-jadwal {--url=https://sipp.pn-natuna.go.id/list_jadwal_sidang} {--date= : Tanggal sidang format YYYY-MM-DD untuk sinkron tanggal tertentu}';
    protected $description = 'Fetch jadwal sidang publik SIPP PN Natuna ke cache lokal jadwal_sidangs.';

    public function handle(): int
    {
        $url = $this->option('url');
        if ($date = $this->option('date')) {
            $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
            $url = 'https://sipp.pn-natuna.go.id/list_jadwal_sidang/search/1/' . $parsedDate->format('d/m/Y');
        }
        try {
            $html = Http::timeout(30)->retry(2, 1000)->withHeaders(['User-Agent' => 'PN-Natuna-Tamu/1.0'])->get($url)->throw()->body();
            $rows = $this->parseRows($html);
            $count = 0;
            foreach ($rows as $r) {
                $jenisPerkara = null;
                $paraPihak = null;

                if (!empty($r['detil_hash'])) {
                    $detilUrl = 'https://sipp.pn-natuna.go.id/detil_jadwal_sidang/' . $r['detil_hash'];
                    try {
                        $detilHtml = Http::timeout(10)->retry(2, 500)->withHeaders(['User-Agent' => 'PN-Natuna-Tamu/1.0'])->get($detilUrl)->body();
                        libxml_use_internal_errors(true);
                        $detilDom = new \DOMDocument();
                        $detilDom->loadHTML($detilHtml);
                        $detilXpath = new \DOMXPath($detilDom);

                        $pihakList = [];
                        foreach ($detilXpath->query('//table//tr') as $dTr) {
                            $dTds = [];
                            foreach ($detilXpath->query('./td', $dTr) as $dTd) {
                                $dTds[] = trim(preg_replace('/\s+/', ' ', $dTd->textContent));
                            }
                            if (count($dTds) >= 2) {
                                $label = strtolower($dTds[0]);
                                $val = $dTds[1];
                                if (str_contains($label, 'jenis perkara')) {
                                    $jenisPerkara = $val;
                                } elseif (str_contains($label, 'pihak') || str_contains($label, 'terdakwa') || str_contains($label, 'penggugat') || str_contains($label, 'tergugat') || str_contains($label, 'pemohon') || str_contains($label, 'termohon')) {
                                    if (!empty($val)) {
                                        $val = trim(preg_replace('/(?<!^)\s*(\d+\.)/', ' $1', $val));
                                        $pihakList[] = $dTds[0] . ': ' . $val;
                                    }
                                }
                            }
                        }
                        if (!empty($pihakList)) {
                            $paraPihak = implode(', ', $pihakList);
                        }
                    } catch (\Throwable $de) {
                        // ignore detail error
                    }
                }

                $data = [
                    'tanggal_sidang' => $r['tanggal_sidang'],
                    'nomor_perkara' => $r['nomor_perkara'],
                    'agenda_sidang' => $r['agenda_sidang'],
                    'ruang_sidang' => $r['ruang_sidang'],
                    'jenis_perkara' => $jenisPerkara,
                    'para_pihak' => $paraPihak,
                    'sumber_data' => 'SIPP PN Natuna'
                ];

                $jadwal = JadwalSidang::whereDate('tanggal_sidang', $r['tanggal_sidang'])
                    ->where('nomor_perkara', $r['nomor_perkara'])
                    ->first();

                if ($jadwal) {
                    $jadwal->update($data);
                } else {
                    JadwalSidang::create($data);
                }
                $count++;
            }
            Cache::forget('jadwal_sidang_hari_ini');
            SyncLog::create(['sumber' => 'SIPP PN Natuna', 'status' => 'success', 'pesan' => 'Berhasil sinkron ' . $count . ' jadwal dari ' . $url, 'synced_at' => now()]);
            $this->info('Sinkron ' . $count . ' jadwal.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            SyncLog::create(['sumber' => 'SIPP PN Natuna', 'status' => 'failed', 'pesan' => Str::limit($e->getMessage(), 1000), 'synced_at' => now()]);
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }

    private function parseRows(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $rows = [];
        foreach ($xpath->query('//table//tr') as $tr) {
            $tds = [];
            foreach ($xpath->query('./td', $tr) as $td) {
                $tds[] = trim(preg_replace('/\s+/', ' ', $td->textContent));
            }
            if (count($tds) < 6) continue;
            if (!preg_match('/\d+\/.*\/PN\s*Ntn/i', $tds[2] ?? '')) continue;
            $tanggal = $this->parseTanggal($tds[1]);
            if (!$tanggal) continue;

            $detilHash = null;
            $links = $xpath->query('./td[7]//a', $tr);
            if ($links->length > 0) {
                $onclick = $links->item(0)->getAttribute('onclick');
                if (preg_match('/detilSidang\(\'([^\']+)\'\)/', $onclick, $m)) {
                    $detilHash = $m[1];
                }
            }

            $rows[] = [
                'tanggal_sidang' => $tanggal,
                'nomor_perkara' => $tds[2],
                'ruang_sidang' => $tds[4] ?? null,
                'agenda_sidang' => $tds[5] ?? null,
                'detil_hash' => $detilHash
            ];
        }
        return $rows;
    }

    private function parseTanggal(?string $text): ?string
    {
        if (!$text) return null;
        $bulan = [
            'Jan' => '01',
            'Feb' => '02',
            'Mar' => '03',
            'Apr' => '04',
            'Mei' => '05',
            'Jun' => '06',
            'Jul' => '07',
            'Agu' => '08',
            'Sep' => '09',
            'Okt' => '10',
            'Nov' => '11',
            'Des' => '12'
        ];
        if (preg_match('/(\d{1,2})\s+([A-Za-z]{3})\.?\s+(\d{4})/u', $text, $m)) {
            $b = $bulan[$m[2]] ?? null;
            if ($b) return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$b, (int)$m[1]);
        }
        return null;
    }
}
