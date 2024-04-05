<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(StatusSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleUserSeeder::class);
        $this->call(EstadoSeeder::class);
        $this->call(CidadeSeeder::class);
        $this->call(EnderecoSeeder::class);
        $this->call(PaisesSeeder::class);
        $this->call(MoedaSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(MetodoPagamentoSeeder::class);
        $this->call(TipoPrivacidadeSeeder::class);
        //$this->call(ReportarBugSeeder::class);
        //$this->call(ViagemSeeder::class);

    }
}
