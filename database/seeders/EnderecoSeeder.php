<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnderecoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endereco = array (
            ['cep' => '22230-001', 'bairro' => 'Flamengo', 'endereco' => 'Rua Senador Vergueiro', 'numero' => '56', 'complemento' => 'apt 103', 'id_cidade' => 3658, 'user_id' => 3 ],
            ['cep' => '22230-001', 'bairro' => 'Flamengo', 'endereco' => 'Rua Senador Vergueiro', 'numero' => '56', 'complemento' => 'apt 103', 'id_cidade' => 3658, 'user_id' => 4 ],
            ['cep' => '22230-001', 'bairro' => 'Flamengo', 'endereco' => 'Rua Senador Vergueiro', 'numero' => '56', 'complemento' => 'apt 103', 'id_cidade' => 3658, 'user_id' => 5 ],
            ['cep' => '22230-001', 'bairro' => 'Flamengo', 'endereco' => 'Rua Senador Vergueiro', 'numero' => '56', 'complemento' => 'apt 103', 'id_cidade' => 3658, 'user_id' => 6 ],
          );

        DB::table('endereco')->insert($endereco);
    }
}
