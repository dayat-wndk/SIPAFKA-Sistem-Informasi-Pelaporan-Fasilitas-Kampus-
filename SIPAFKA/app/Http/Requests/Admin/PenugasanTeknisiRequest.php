<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PenugasanTeknisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('catatan'))) {
            $this->merge([
                'catatan' => trim($this->input('catatan')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'teknisi_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'teknisi')
                    ->where('aktif', true),
            ],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'teknisi_id.required' => 'Teknisi penanggung jawab wajib dipilih.',
            'teknisi_id.integer' => 'Pilihan teknisi tidak valid.',
            'teknisi_id.exists' => 'Pengguna yang dipilih bukan teknisi aktif yang valid.',
            'catatan.max' => 'Catatan penugasan maksimal 1000 karakter.',
        ];
    }
}
