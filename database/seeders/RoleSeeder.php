<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array (
            ['name' => 'ROOT', 'display_name' => 'Usuário Root', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'ADMIN', 'display_name' => 'Usuário Admin', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'BASICO', 'display_name' => 'Usuário básico', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'AVANCADO', 'display_name' => 'Usuário Avançado', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
          );



        DB::table('roles')->insert($roles);
    }
}
