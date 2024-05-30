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
    // php artisan test --filter=test_user_has_permission_status

    use RefreshDatabase;
    // Roles - ROOT | ADMIN | BASICO | AVANCADO

    private User $userAdmin;
    private User $userBasico;
    private $header;
    private $headerBasico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userAdmin = $this->createUser(['name' => 'ROOT']);
        $this->header = $this->createHeaderToken($this->userAdmin);

    }

    public function test_user_has_permission_buscar_por_id_status(): void
    {
        $this->userBasico = $this->createUser(['name' => 'BASICO']);
        $this->headerBasico = $this->createHeaderToken($this->userBasico);

        $response = $this->actingAs($this->userBasico)->getJson('api/status/buscar-status/1', $this->headerBasico);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_buscar_por_id_status(): void
    {
        $response = $this->actingAs($this->userAdmin)->getJson('api/status/buscar-status/1', $this->header);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_has_permission_pagination_status(): void
    {
        $this->userBasico = $this->createUser(['name' => 'BASICO']);
        $this->headerBasico = $this->createHeaderToken($this->userBasico);

        $response = $this->actingAs($this->userBasico)->getJson('api/status/listar-status/1/10/desc', $this->headerBasico);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_lista_status_paginacao(): void
    {
        $response = $this->actingAs($this->userAdmin)->getJson('api/status/listar-status/1/10/desc', $this->header);
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function createUser($role): User
    {
        $role_admin = Role::factory()->create($role);
        Status::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role_admin);
        return $user;
    }

    protected function createHeaderToken($usuario): array
    {

        // Alterando os dados do usuario
        //$repository = new StatusRepository($this->status);
        $credencials = [
            'email' => $usuario->email,
            'password' => '1q2w3e4r',
        ];

        $token = Auth::attempt($credencials);

        $headers = array_merge(
            ['Authorization' => 'Bearer '.$token],
        );

        return $headers;

    }
}
