<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'AC', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah B.204', 'keterangan' => 'Unit pendingin ruang kuliah'],
            ['nama' => 'AC', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah A.101', 'keterangan' => 'Unit pendingin ruang kuliah'],
            ['nama' => 'Proyektor', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah B.201', 'keterangan' => 'Proyektor LCD terpasang di plafon'],
            ['nama' => 'Proyektor', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah B.204', 'keterangan' => 'Proyektor LCD ruang kuliah'],
            ['nama' => 'Lampu', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Lab. Kimia Dasar', 'keterangan' => 'Penerangan utama laboratorium'],
            ['nama' => 'Kursi kuliah', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah A.101', 'keterangan' => 'Kursi mahasiswa lipat'],
            ['nama' => 'Kran air', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Toilet FMIPA lt. 1', 'keterangan' => 'Kran wastafel lantai 1'],
            ['nama' => 'Lampu jalan', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Area Parkir FMIPA', 'keterangan' => 'Penerangan area parkir timur'],
            ['nama' => 'Stopkontak', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Laboratorium Fisika', 'keterangan' => 'Stopkontak meja praktikum'],
            ['nama' => 'Pintu', 'gedung' => 'Gedung FMIPA', 'ruang' => 'Ruang kuliah A.103', 'keterangan' => 'Pintu utama ruang kuliah'],
        ];

        foreach ($items as $item) {
            Fasilitas::firstOrCreate(
                ['nama' => $item['nama'], 'gedung' => $item['gedung'], 'ruang' => $item['ruang']],
                ['keterangan' => $item['keterangan']]
            );
        }
    }
}
