<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $usersSystem = array (
            ['name' => 'Root', 'email' => 'vitoreselecao@gmail.com', 'telefone' => '(21) 98807-7118' , 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'Admin', 'email' => 'admin@admin.com.br', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User Básico 1', 'email' => 'basico1@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User Básico 2', 'email' => 'basico2@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User Avançado 1', 'email' => 'avancado1@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User Avançado 2', 'email' => 'avancado2@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
        );

        DB::table('users')->insert($usersSystem);
    }
}
