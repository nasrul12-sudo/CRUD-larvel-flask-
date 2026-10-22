<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employee')->insert([
            ['Id_employee' => 1942, 'entry_date' => '2025-06-20 14:33:37', 'nama' => 'Ahmad Husaini', 'rank' => 'Operator', 'gender' => 'L'],
            ['Id_employee' => 1943, 'entry_date' => '2025-06-20 14:33:40', 'nama' => 'Maya Sofa Nata', 'rank' => 'Operator', 'gender' => 'P'],
            ['Id_employee' => 1944, 'entry_date' => '2025-06-20 14:33:43', 'nama' => 'Rizal Ardiyansah Bintoro', 'rank' => 'Operator', 'gender' => 'L'],
        ]);

        DB::table('product')->insert([
            ['Id_product' => 128409, 'sub_category' => 'CALP',  'serial_no' => 110043, 'description' => 'Cincin Anak Laki Putus 1 gr-10043-20K',     'carat' => '20K - 875'],
            ['Id_product' => 767072, 'sub_category' => 'LT',    'serial_no' => 110237, 'description' => 'Liontin 1 gr-10237-17K',                    'carat' => '17K - 750'],
            ['Id_product' => 772839, 'sub_category' => 'CWTMT', 'serial_no' => 10152,  'description' => 'Cincin Wanita Mata-10152-16K',              'carat' => '16K - 700'],
            ['Id_product' => 772893, 'sub_category' => 'GPMA',  'serial_no' => 210125, 'description' => 'Gelang Pipa Mata Anak 2 gr-1012508K',       'carat' => '8K - 375'],
            ['Id_product' => 877501, 'sub_category' => 'KCCBM', 'serial_no' => 210047, 'description' => 'Kepala Cor Cincin Bangkok Metro 2 gr-10047','carat' => '20K - 875'],
        ]);
    }
}