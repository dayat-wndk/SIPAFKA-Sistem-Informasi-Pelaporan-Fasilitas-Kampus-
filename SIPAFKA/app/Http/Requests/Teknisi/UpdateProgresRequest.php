<?php

namespace App\Http\Requests\Teknisi;

use App\Enums\StatusLaporan;
use App\Models\Laporan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgresRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $laporan = $this->route('laporan');

        if (! $user || $user->role !== 'teknisi' || ! $user->aktif) {
            return false;
        }

        if (! $laporan instanceof Laporan) {
            return false;
        }

        $laporan->loadMissing('penugasan');

        return (int) ($laporan->penugasan?->teknisi_id ?? 0) === (int) $user->id;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    StatusLaporan::Diproses->value,
                    StatusLaporan::Selesai->value,
                ]),
            ],
            'catatan' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.in' => 'Pilihan status teknisi hanya boleh Diproses atau Selesai.',
            'catatan.required' => 'Catatan perkembangan wajib diisi.',
            'catatan.string' => 'Catatan perkembangan harus berupa teks.',
            'catatan.min' => 'Catatan perkembangan minimal 10 karakter.',
            'catatan.max' => 'Catatan perkembangan maksimal 2000 karakter.',
        ];
    }
}
