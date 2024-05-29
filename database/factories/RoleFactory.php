<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['name' => 'ROOT', 'display_name' => 'Usuário Root', 'description' => '', 'created_at' => '2023-06-12 02:55:18', 'updated_at' => '2023-06-12 02:55:18'];

    }

}
