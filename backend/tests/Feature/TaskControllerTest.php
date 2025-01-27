<?php

namespace Tests\Feature;

use App\Mail\SharedTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
/* use App\Mail\SharedTask;
use Illuminate\Support\Facades\Mail; */


class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_store_task()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Generar un token JWT para el usuario
        $token = JWTAuth::fromUser($user);


        $taskData = [
            'title' => 'comprar camisa de santa fe',
            'description' => 'es la nueva camisa de santa fe para este año',
            'user_id' => $user->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/create-tasks', $taskData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'task' => ['id', 'title', 'description'],
                ],
                'status',
                'message',
                'errors',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'comprar camisa de santa fe',
            'description' => 'es la nueva camisa de santa fe para este año',
        ]);
    }

    public function test_update_task_status()
    {
        Mail::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);


        $updateData = [
            'user_id' => $user->id,
            'status' => 'En progreso',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson("/api/tasks/{$task->id}/update-status", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'status' => 'En progreso',
        ]);

        Mail::assertQueued(SharedTask::class, function ($mail) use ($user, $task) {
            return $mail->hasTo($user->email) &&
                $mail->task->id === $task->id;
        });
    }

    public function test_get_task_history()
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task->users()->attach($user->id, ['status' => 'Creada', 'created_at' => now()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/historyTask/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'task' => [
                        'id',
                        'title',
                        'users' => [
                            '*' => [
                                'id',
                                'name',
                                'email',
                                'pivot' => [
                                    'task_id',
                                    'user_id',
                                    'status',
                                    'updated_at',
                                    'created_at',
                                ]
                            ]
                        ]
                    ]
                ],
                'status',
                'message',
                'errors'
            ]);
    }

    public function test_get_all_tasks_with_last_status()
    {
        // Crear tareas con estados
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task1->users()->attach($user->id, ['status' => 'Creada', 'created_at' => now()]);
        $task2->users()->attach($user->id, ['status' => 'En progreso', 'created_at' => now()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/tasks/last-status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'task' => [
                        [
                            'task' => [
                                'id',
                                'title',
                                'description',
                                'created_at',
                                'updated_at',
                                'users' => [
                                    '*' => [
                                        'id',
                                        'name',
                                        'email',
                                        'pivot' => [
                                            'task_id',
                                            'user_id',
                                            'status',
                                            'updated_at',
                                            'created_at',
                                        ]
                                    ]
                                ]
                            ],
                            'last_status'
                        ]
                    ]
                ],
                'status',
                'message',
                'errors'
            ]);
    }

    public function test_update_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Datos para actualizar la tarea
        $updateData = [
            'title' => 'Tarea actualizada',
            'description' => 'Descripción actualizada',
        ];

        // Hacer la solicitud PUT
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/tasks/{$task->id}", $updateData);

        // Verificar que la respuesta sea correcta
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'task' => [
                        'title' => 'Tarea actualizada',
                    ],
                ],
            ]);

        // Verificar que la tarea se haya actualizado en la base de datos
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarea actualizada',
            'description' => 'Descripción actualizada',
        ]);
    }

    public function test_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)->assertJson([]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
