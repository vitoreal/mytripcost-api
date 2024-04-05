<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $status = array(
            ['nome' => 'Ativo'],
            ['nome' => 'Inativo'],
            ['nome' => 'Aguardando aprovação'],
        );

        DB::table('status')->insert($status);
    }
}
