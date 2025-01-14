<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id', // Usuario creador de la tarea
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        // Registrar en la tabla pivot con status "Creada"
        $task->users()->attach($validated['user_id'], ['status' => 'Creada']);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ]);;
    }

    public function updateTaskStatus(Request $request, $taskId)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Usuario que modifica la tarea
            'status' => 'required|string|max:255', // Nuevo estado de la tarea
        ]);

        // Buscar la tarea
        $task = Task::findOrFail($taskId);

        // Crear un nuevo registro en la tabla pivot
        $task->users()->attach($validated['user_id'], [
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Task status updated successfully',
        ]);
    }

    public function historyTaskId($taskId)
    {
        /* // Buscar la tarea
        $task = Task::with(['users' => function ($query) {
            $query->withPivot('status', 'created_at')
                ->orderBy('pivot_created_at', 'asc');
        }])->findOrFail($taskId);

        return response()->json([
            'task' => $task->title,
            'history' => $task->users->map(function ($user) {
                return [
                    'user' => $user->name,
                    'status' => $user->pivot->status,
                    'changed_at' => $user->pivot->created_at,
                ];
            }),
        ]); */

        // Buscar la tarea
        $task = Task::findOrFail($taskId);

        // Obtener el historial actualizado
        $taskWithUsers = $task->load('users');

        return response()->json([
            'message' => 'Task history',
            'task' => $taskWithUsers,
        ]);
    }

    public function getAllTasksWithLastStatus()
    {
        // Obtener todas las tareas con los usuarios asociados
        $tasks = Task::with(['users' => function ($query) {
            $query->latest('pivot_updated_at') // Ordenar por la fecha de la última actualización en la tabla pivot
                ->take(1); // Tomar solo el último estado
        }])->get();

        // Formatear las tareas para incluir solo el último estado
        $tasksWithLastStatus = $tasks->map(function ($task) {
            // Obtener el último estado de la tarea desde los usuarios
            $lastStatus = $task->users->first()?->pivot->status;

            return [
                'task' => $task,
                'last_status' => $lastStatus,
            ];
        });

        return response()->json($tasksWithLastStatus);
    }

    public function updateTask(Request $request, $taskId)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Buscar la tarea por ID
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Actualizar los valores de la tarea
        $task->title = $validatedData['title'];
        $task->description = $validatedData['description'];

        // Guardar los cambios
        $task->save();

        return response()->json([
            'message' => 'Task updated successfully',
        ]);
    }

    public function deleteTask($taskId)
    {
        // Buscar la tarea por ID
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Eliminar la tarea
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
