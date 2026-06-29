<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestRequest;
use App\Models\Guest;
use App\Models\JadwalSidang;
use App\Models\SurveyLink;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function index()
    {
        $links = SurveyLink::where('aktif', true)->pluck('url', 'nama_survey');
        return view('landing', compact('links'));
    }

    public function create()
    {
        $jadwalSidangs = Cache::remember('jadwal_sidang_hari_ini', 300, function () {
            return JadwalSidang::whereDate('tanggal_sidang', today())->orderBy('jam_sidang')->get();
        });

        $captchaLeft = random_int(1, 9);
        $captchaRight = random_int(1, 9);
        session(['guest_captcha_answer' => $captchaLeft + $captchaRight]);

        return view('buku-tamu.form', compact('jadwalSidangs', 'captchaLeft', 'captchaRight'));
    }

    public function store(StoreGuestRequest $request)
    {
        $data = $request->validated();
        if (($data['pekerjaan'] ?? '') === 'Lainnya' && !empty($data['pekerjaan_lainnya'])) {
            $data['pekerjaan'] = $data['pekerjaan_lainnya'];
        }
        unset($data['pekerjaan_lainnya']);

        if (($data['jenis_layanan'] ?? '') === 'Menghadiri Sidang' && !empty($data['jadwal_sidang_id'])) {
            $j = JadwalSidang::findOrFail($data['jadwal_sidang_id']);
            $data['nomor_perkara'] = $j->nomor_perkara;
            $data['agenda_sidang'] = $j->agenda_sidang;
            $data['ruang_sidang'] = $j->ruang_sidang;
            $data['jam_sidang'] = $j->jam_sidang;
        }
        unset($data['captcha']);

        $guest = Guest::create($data);
        $request->session()->forget('guest_captcha_answer');

        return redirect()->route('buku-tamu.selesai', $guest->kode_kunjungan);
    }

    public function success(string $kode_kunjungan)
    {
        $guest = Guest::where('kode_kunjungan', $kode_kunjungan)->firstOrFail();
        return view('buku-tamu.selesai', compact('guest'));
    }

    public function cekTamu(\Illuminate\Http\Request $request)
    {
        $tanggal = $request->query('tanggal');
        
        // Validate date format, fallback to today
        if ($tanggal) {
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal);
            } catch (\Exception $e) {
                $date = today();
            }
        } else {
            $date = today();
        }

        $dateStr = $date->toDateString();

        // Get all court sessions for the selected date
        $jadwalSidangs = JadwalSidang::whereDate('tanggal_sidang', $dateStr)
            ->orderBy('jam_sidang')
            ->get();

        // Load guests who registered on the selected date
        $jadwalSidangs->load(['guests' => function ($query) use ($dateStr) {
            $query->whereDate('created_at', $dateStr);
        }]);

        return view('cek', compact('jadwalSidangs', 'dateStr'));
    }
}
