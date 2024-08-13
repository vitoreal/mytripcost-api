<?php

namespace Database\Seeders;

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
            ['name' => 'USER_ROOT', 'display_name' => 'Usuário Root', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'USER_ADMIN', 'display_name' => 'Usuário Admin', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'USER_BASICO', 'display_name' => 'Usuário básico', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
            ['name' => 'USER_AVANCADO', 'display_name' => 'Usuário Avançado', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'],
          );



        DB::table('roles')->insert($roles);
    }
}
