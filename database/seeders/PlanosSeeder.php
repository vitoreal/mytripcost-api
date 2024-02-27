<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $planos = array(
            ['nome' => 'Plano Básico', 'calcular_moeda' => 0, 'qtd_amigos' => 0,'qtd_foto' => 5, 'qtd_categoria' => 0],
            ['nome' => 'Plano Completo', 'calcular_moeda' => 1, 'qtd_amigos' => 50,'qtd_foto' => 30, 'qtd_categoria' => 10],
        );

        DB::table('planos')->insert($planos);
    }
}
