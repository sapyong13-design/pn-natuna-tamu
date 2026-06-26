<?php
namespace Database\Seeders;
use App\Models\JadwalSidang; use Illuminate\Database\Seeder;
class JadwalSidangSeeder extends Seeder { public function run(): void { JadwalSidang::firstOrCreate(['tanggal_sidang'=>today(),'nomor_perkara'=>'1/Pdt.G/2026/PN Ran'],['jam_sidang'=>'09:00','para_pihak'=>'Penggugat vs Tergugat','agenda_sidang'=>'Pembuktian','ruang_sidang'=>'Ruang Sidang Utama','majelis_hakim'=>'Majelis A','sumber_data'=>'Dummy Seeder']); JadwalSidang::firstOrCreate(['tanggal_sidang'=>today(),'nomor_perkara'=>'2/Pid.B/2026/PN Ran'],['jam_sidang'=>'10:00','para_pihak'=>'Penuntut Umum vs Terdakwa','agenda_sidang'=>'Pembacaan Tuntutan','ruang_sidang'=>'Ruang Sidang 2','majelis_hakim'=>'Majelis B','sumber_data'=>'Dummy Seeder']); } }
