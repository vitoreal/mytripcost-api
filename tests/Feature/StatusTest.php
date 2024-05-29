<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use \Illuminate\Http\Response;
use \Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StatusTest extends TestCase
{

    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    public function test_homepage_contains_empty_table(): void
    {

        // Alterando os dados do usuario
        //$repository = new StatusRepository($this->status);
        $credencials = [
                'email' => $this->user->email,
                'password' => '1q2w3e4r',
            ];

        $token = Auth::attempt($credencials);

        $headers = array_merge(
            ['Authorization' => 'Bearer '.$token],
        );

        $response = $this->actingAs($this->user, 'api')->getJson('api/status/listar-status/1/10/desc', $headers);
        $response->assertStatus(Response::HTTP_OK);

        //$response = $this->actingAs($this->user)->get('status/listar-status/1/10/desc');

    }

    protected function createUser(): User
    {
        $role_admin = Role::factory()->create(['name' => 'ROOT teste']);
        Status::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role_admin);
        return $user;
    }
}
