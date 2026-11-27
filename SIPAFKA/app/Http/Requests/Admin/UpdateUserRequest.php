<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'role' => ['required', 'in:pelapor,admin,teknisi'],
            'password' => ['nullable', 'string', 'min:8'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 120 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 150 karakter.',
            'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna tidak valid.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'aktif.boolean' => 'Status akun tidak valid.',
        ];
    }
}
