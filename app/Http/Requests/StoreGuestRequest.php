<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tamu' => ['required', 'string', 'max:255'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'pekerjaan_lainnya' => ['nullable', 'required_if:pekerjaan,Lainnya', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat_instansi' => ['required', 'string', 'max:255'],
            'jenis_layanan' => ['required', Rule::in(['Menghadiri Sidang'])],
            'keperluan' => ['nullable', 'string', 'max:1000'],
            'jadwal_sidang_id' => ['nullable', 'required_if:jenis_layanan,Menghadiri Sidang', 'exists:jadwal_sidangs,id'],
            'peran_sidang' => ['nullable', 'required_if:jenis_layanan,Menghadiri Sidang', Rule::in(Guest::PERAN_SIDANG)],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
