<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFasilitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => trim((string) ($this->input('nama') ?? $this->input('nama_fasilitas', ''))),
            'lokasi' => trim((string) ($this->input('lokasi') ?? $this->input('ruang', ''))),
            'gedung' => trim((string) $this->input('gedung', 'Gedung FMIPA')) ?: 'Gedung FMIPA',
            'keterangan' => $this->filled('keterangan') ? trim((string) $this->input('keterangan')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'lokasi' => ['required', 'string', 'max:150'],
            'gedung' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama fasilitas wajib diisi.',
            'nama.max' => 'Nama fasilitas maksimal 120 karakter.',
            'lokasi.required' => 'Lokasi FMIPA wajib diisi.',
            'lokasi.max' => 'Lokasi FMIPA maksimal 150 karakter.',
            'gedung.max' => 'Nama gedung maksimal 120 karakter.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ];
    }
}
