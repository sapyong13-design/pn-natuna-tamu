<?php

namespace App\Exports;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class GuestsExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles, 
    WithCustomStartCell, 
    WithColumnFormatting,
    WithEvents
{
    public function __construct(
        public ?string $tanggalAwal = null, 
        public ?string $tanggalAkhir = null, 
        public ?string $jenisLayanan = null
    ){}

    public function query()
    {
        return Guest::query()
            ->when($this->tanggalAwal, fn(Builder $q) => $q->whereDate('waktu_kedatangan', '>=', $this->tanggalAwal))
            ->when($this->tanggalAkhir, fn(Builder $q) => $q->whereDate('waktu_kedatangan', '<=', $this->tanggalAkhir))
            ->when($this->jenisLayanan, fn(Builder $q) => $q->where('jenis_layanan', $this->jenisLayanan))
            ->orderBy('waktu_kedatangan');
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Kedatangan',
            'Kode Kunjungan',
            'Nama Tamu',
            'Pekerjaan',
            'No HP',
            'Alamat/Instansi',
            'Jenis Layanan',
            'Keperluan',
            'Peran Sidang',
            'Nomor Perkara',
            'Agenda Sidang',
            'Ruang Sidang',
            'Jam Sidang'
        ];
    }

    public function map($g): array
    {
        return [
            $g->waktu_kedatangan ? $g->waktu_kedatangan->format('Y-m-d') : $g->created_at->format('Y-m-d'),
            $g->waktu_kedatangan ? $g->waktu_kedatangan->format('H:i') : $g->created_at->format('H:i'),
            $g->kode_kunjungan,
            $g->nama_tamu,
            $g->pekerjaan,
            $g->no_hp ? ' ' . $g->no_hp : '', // Leading space to preserve leading zero in Excel
            $g->alamat_instansi,
            $g->jenis_layanan,
            $g->keperluan,
            $g->peran_sidang ?? '-',
            $g->nomor_perkara ?? '-',
            $g->agenda_sidang ?? '-',
            $g->ruang_sidang ?? '-',
            $g->jam_sidang ?? '-'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TEXT, // Column F (No HP) formatted as text
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row (A4 to N4)
            4 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '022C22'], // Deep Court Green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // 1. Add Title Header
                $sheet->mergeCells('A1:N1');
                $sheet->setCellValue('A1', 'REKAPITULASI KUNJUNGAN BUKU TAMU DIGITAL');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('022C22'));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('A2:N2');
                $sheet->setCellValue('A2', 'PENGADILAN NEGERI NATUNA KELAS II');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('4B5563'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Add period metadata
                $period = 'Semua Periode';
                if ($this->tanggalAwal && $this->tanggalAkhir) {
                    $period = 'Periode: ' . $this->tanggalAwal . ' s/d ' . $this->tanggalAkhir;
                } elseif ($this->tanggalAwal) {
                    $period = 'Periode Mulai: ' . $this->tanggalAwal;
                } elseif ($this->tanggalAkhir) {
                    $period = 'Periode Sampai: ' . $this->tanggalAkhir;
                }
                
                $sheet->mergeCells('A3:N3');
                $sheet->setCellValue('A3', $period . ($this->jenisLayanan ? ' | Layanan: ' . $this->jenisLayanan : ''));
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6B7280'));
                
                // 2. Set Row Heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(32); // Table header
                
                // 3. Alignments and Row Heights
                $highestRow = $sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(22);
                    
                    // Alignments
                    $centerCols = ['A', 'B', 'C', 'F', 'J', 'K', 'M', 'N'];
                    foreach ($centerCols as $col) {
                        $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
                
                // 4. Apply grid borders to table data (A4 to N[HighestRow])
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ];
                $sheet->getStyle('A4:N' . $highestRow)->applyFromArray($styleArray);
            },
        ];
    }
}
