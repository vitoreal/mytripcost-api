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

class AuthTest extends TestCase
{

    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    public function test_login()
    {

        $response = $this->post('api/login/', [
            'email' => $this->user->email,
            'password' => '1q2w3e4r',
        ]);
        //$this->user->roles[0]->name
        $response->assertStatus(Response::HTTP_OK);
        $this->assertAuthenticatedAs($this->user);

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
