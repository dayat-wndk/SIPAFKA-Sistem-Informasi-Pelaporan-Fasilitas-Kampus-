<?php

namespace App\Http\Requests\Pelapor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'pelapor';
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'fasilitas_id' => ['required', 'integer', 'exists:fasilitas,id'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul laporan wajib diisi.',
            'judul.string' => 'Judul laporan harus berupa teks.',
            'judul.max' => 'Judul laporan maksimal 150 karakter.',
            'deskripsi.required' => 'Deskripsi kerusakan wajib diisi.',
            'deskripsi.string' => 'Deskripsi kerusakan harus berupa teks.',
            'deskripsi.min' => 'Deskripsi kerusakan minimal 10 karakter.',
            'fasilitas_id.required' => 'Fasilitas dan lokasi wajib dipilih.',
            'fasilitas_id.integer' => 'Pilihan fasilitas tidak valid.',
            'fasilitas_id.exists' => 'Fasilitas atau lokasi yang dipilih tidak ditemukan.',
            'foto.required' => 'Foto kerusakan wajib diunggah.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
