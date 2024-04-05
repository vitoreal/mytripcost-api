<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoedaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moeda = array (
            ['codigo' => 'EUR', 'nome' => 'Euro'],
            ['codigo' => 'USD', 'nome' => 'Dólar americano'],
            ['codigo' => 'JPY', 'nome' => 'Yen japonês'],
            ['codigo' => 'BGN', 'nome' => 'Lev Búlgaro'],
            ['codigo' => 'CZK', 'nome' => 'Coroa Checa'],
            ['codigo' => 'DKK', 'nome' => 'Coroa Dinamarquesa'],
            ['codigo' => 'GBP', 'nome' => 'Libra Esterlina Britânica'],
            ['codigo' => 'HUF', 'nome' => 'Forint Húngaro'],
            ['codigo' => 'PLN', 'nome' => 'Zloty polonês'],
            ['codigo' => 'RON', 'nome' => 'Leu romeno'],
            ['codigo' => 'SEK', 'nome' => 'Coroa Sueca'],
            ['codigo' => 'CHF', 'nome' => 'Franco suíço'],
            ['codigo' => 'ISK', 'nome' => 'Coroa Islandesa'],
            ['codigo' => 'NOK', 'nome' => 'Coroa Norueguesa'],
            ['codigo' => 'HRK', 'nome' => 'Kuna Croata'],
            ['codigo' => 'RUB', 'nome' => 'rublo russo'],
            ['codigo' => 'TRY', 'nome' => 'Lira turca'],
            ['codigo' => 'AUD', 'nome' => 'Dólar australiano'],
            ['codigo' => 'BRL', 'nome' => 'real brasileiro'],
            ['codigo' => 'CAD', 'nome' => 'Dólar canadense'],
            ['codigo' => 'CNY', 'nome' => 'Yuan chinês'],
            ['codigo' => 'HKD', 'nome' => 'Dólar de Hong Kong'],
            ['codigo' => 'IDR', 'nome' => 'Rupia Indonésia'],
            ['codigo' => 'ILS', 'nome' => 'Novo Sheqel israelense'],
            ['codigo' => 'INR', 'nome' => 'Rupia indiana'],
            ['codigo' => 'KRW', 'nome' => 'Won sul-coreano'],
            ['codigo' => 'MXN', 'nome' => 'Peso Mexicano'],
            ['codigo' => 'MYR', 'nome' => 'Ringgit malaio'],
            ['codigo' => 'NZD', 'nome' => 'Dólar da Nova Zelândia'],
            ['codigo' => 'PHP', 'nome' => 'Peso filipino'],
            ['codigo' => 'SGD', 'nome' => 'Dólar de Singapura'],
            ['codigo' => 'THB', 'nome' => 'Baht tailandês'],
            ['codigo' => 'ZAR', 'nome' => 'Rand sul-africano'],


        );

        DB::table('moeda')->insert($moeda);
    }
}
