<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function createTask(array $data)
    {
        return DB::transaction(function () use ($data) {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $task->users()->attach($data['user_id'], ['status' => 'Creada']);

            return $task;
        });
    }

    public function updateTaskStatus(int $taskId, array $data)
    {
        $task = DB::transaction(function () use ($taskId, $data) {
            $task = Task::findOrFail($taskId);
            $task->users()->attach($data['user_id'], [
                'status' => $data['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            return $task;
        });

        return $task;
    }

    /**
     * Obtener un usuario por su ID.
     *
     * @param int $userId
     * @return User
     * @throws ModelNotFoundException
     */
    public function getUserById(int $userId): User
    {
        return User::findOrFail($userId);
    }

    public function getTaskHistory(int $taskId)
    {
        $task = Task::with(['users' => function ($query) {
            $query->withPivot('status', 'created_at')
                ->orderBy('pivot_created_at', 'asc');
        }])->findOrFail($taskId);

        return $task;
    }

    public function getAllTasksWithLastStatus()
    {
        $tasks = Task::with(['users' => function ($query) {
            $query->latest('pivot_updated_at')
                ->take(1);
        }])->get();

        return $tasks->map(function ($task) {
            return [
                'task' => $task,
                'last_status' => $task->users->first()?->pivot->status,
            ];
        });
    }

    public function updateTask(int $taskId, array $data)
    {
        $task = Task::findOrFail($taskId);
        $task->update($data);

        return $task;
    }

    public function deleteTask(int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->delete();

        return $task;
    }
}
