<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categorias = array (
            ['nome' => 'Alojamento'],
            ['nome' => 'Atividades'],
            ['nome' => 'Bebidas'],
            ['nome' => 'Café'],
            ['nome' => 'Comestíveis'],
            ['nome' => 'Compras'],
            ['nome' => 'Entretenimento'],
            ['nome' => 'Geral'],
            ['nome' => 'Lavanderia'],
            ['nome' => 'Restaurantes'],
            ['nome' => 'Taxas & Encargos'],
            ['nome' => 'Taxas de Câmbio'],
            ['nome' => 'Transportes'],
            ['nome' => 'Visitas'],
            ['nome' => 'Vôos'],

        );

        DB::table('categoria')->insert($categorias);
    }
}
