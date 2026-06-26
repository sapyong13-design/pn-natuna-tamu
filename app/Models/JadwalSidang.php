<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model;
class JadwalSidang extends Model { use HasFactory; protected $fillable=['tanggal_sidang','jam_sidang','nomor_perkara','jenis_perkara','para_pihak','agenda_sidang','ruang_sidang','majelis_hakim','sumber_data'];protected $casts=['tanggal_sidang'=>'date','jam_sidang'=>'datetime:H:i']; public function guests(){return $this->hasMany(Guest::class);} public function getLabelAttribute(): string { return trim(($this->jam_sidang ? $this->jam_sidang.' - ' : '').$this->nomor_perkara.' - '.($this->agenda_sidang ?? '').' - '.($this->ruang_sidang ?? '')); } }
