<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register()
    {
        // Datos del usuario
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123456789',
        ];

        // Hacer la solicitud POST
        $response = $this->postJson('/api/register', $userData);

        // Verificar que la respuesta sea correcta
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Verificar que el usuario se haya creado en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('123456789'),
        ]);


        $loginData = [
            'email' => 'john@example.com',
            'password' => '123456789',
        ];


        $response = $this->postJson('/api/login', $loginData);


        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                'token',
            ]);
    }

    public function test_logout()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/logout');

        $response->assertStatus(201)
            ->assertJson(['message' => 'Logout Successfully']);

        $this->assertFalse(JWTAuth::setToken($token)->check());
    }

    public function test_get_all_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();

        $user1->tasks()->attach($task1->id, ['status' => 'Creada', 'created_at' => now()]);
        $user2->tasks()->attach($task2->id, ['status' => 'En progreso', 'created_at' => now()]);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'users' => [
                    '*' => ['id', 'name', 'email', 'tasks'],
                ],
            ]);
    }
}
