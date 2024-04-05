<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPrivacidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tipoPrivacidade = array(
            ['nome' => 'Privado'],
            ['nome' => 'Amigos'],
            ['nome' => 'Todos'],
        );

        DB::table('tipo_privacidade')->insert($tipoPrivacidade);
    }
}
