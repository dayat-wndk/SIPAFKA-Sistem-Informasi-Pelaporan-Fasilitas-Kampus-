<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VerifikasiLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'alasan_penolakan' => is_string($this->input('alasan_penolakan'))
                ? trim($this->input('alasan_penolakan'))
                : $this->input('alasan_penolakan'),
            'catatan_verifikasi' => is_string($this->input('catatan_verifikasi'))
                ? trim($this->input('catatan_verifikasi'))
                : $this->input('catatan_verifikasi'),
        ]);
    }

    public function rules(): array
    {
        $isReject = $this->routeIs('admin.laporan.reject');

        return [
            'alasan_penolakan' => $isReject
                ? ['required', 'string', 'min:5', 'max:1000']
                : ['nullable', 'string', 'max:1000'],
            'catatan_verifikasi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.string' => 'Alasan penolakan harus berupa teks.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 1000 karakter.',
            'catatan_verifikasi.max' => 'Catatan verifikasi maksimal 1000 karakter.',
        ];
    }
}
