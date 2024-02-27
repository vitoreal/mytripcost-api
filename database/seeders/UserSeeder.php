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
            ['name' => 'Root', 'email' => 'vitoreselecao@gmail.com', 'telefone' => '(21) 98807-7118' , 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'Admin', 'email' => 'admin@admin.com.br', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 1', 'email' => 'teste1@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 2', 'email' => 'teste2@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 3', 'email' => 'teste3@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 4', 'email' => 'teste4@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 5', 'email' => 'teste5@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 6', 'email' => 'teste6@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 7', 'email' => 'teste7@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 8', 'email' => 'teste8@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 9', 'email' => 'teste9@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'User teste Padrão 10', 'email' => 'teste10@gmail.com', 'telefone' => '(21) 98807-7118', 'status_id' => 1, 'planos_id' => 1, 'password' => bcrypt('1q2w3e4r'), 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
        );

        DB::table('users')->insert($usersSystem);
    }
}
