<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $metodoPagamento = array (
            ['nome' => 'Boleto Bancário'],
            ['nome' => 'Cartão de crédito'],
            ['nome' => 'Cartão de Débito'],
            ['nome' => 'Cheque'],
            ['nome' => 'Dinheiro'],
            ['nome' => 'Pix'],
            ['nome' => 'Transferência Eletrônica'],
            ['nome' => 'Vale Alimentação'],
            ['nome' => 'Vale Combustível'],
            ['nome' => 'Vale Presente'],
            ['nome' => 'Vale Refeição'],
        );

        DB::table('metodo_pagamento')->insert($metodoPagamento);
    }
}
