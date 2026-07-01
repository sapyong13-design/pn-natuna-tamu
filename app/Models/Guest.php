<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str;
class Guest extends Model { use HasFactory; public const JENIS_LAYANAN=['Menghadiri Sidang','PTSP','Posbakum','Mediasi','Pengambilan Produk Pengadilan','Layanan Lainnya']; public const PERAN_SIDANG=['Para Pihak','Saksi','Pengunjung','Ahli']; protected $fillable=['kode_kunjungan','nama_tamu','pekerjaan','tanggal_lahir','jenis_kelamin','pendidikan_terakhir','no_hp','alamat_instansi','jenis_layanan','keperluan','jadwal_sidang_id','peran_sidang','nomor_perkara','agenda_sidang','ruang_sidang','jam_sidang','keterangan','waktu_kedatangan']; protected $casts=['waktu_kedatangan'=>'datetime','jam_sidang'=>'datetime:H:i','tanggal_lahir'=>'date']; public function jadwalSidang(){return $this->belongsTo(JadwalSidang::class);} protected static function booted(): void { static::creating(function($g){ if(!$g->kode_kunjungan) $g->kode_kunjungan='TAMU-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)); if(!$g->waktu_kedatangan) $g->waktu_kedatangan=now(); }); } 
    public static function formatJamSidang($time): string
    {
        if (!$time) {
            return '-';
        }
        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }
        try {
            return \Carbon\Carbon::parse($time)->format('H:i');
        } catch (\Exception $e) {
            return '-';
        }
    }
}
