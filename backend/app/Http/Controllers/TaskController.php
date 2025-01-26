<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Jobs\TaskStatusJob;
use App\Services\TaskService;
use App\Mail\SharedTask;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function storeTask(StoreTaskRequest $request)
    {

        $validated = $request->validated();

        $task = $this->taskService->createTask($validated);

        return jsonResponse(['task' => $task,]);
    }

    public function updateTaskStatus(UpdateTaskStatusRequest $request, $taskId)
    {
        $validated = $request->validated();

        //TaskStatusJob::dispatch($task, $this->taskService, $taskId, $validated);

        //return jsonResponse(['task' => $data]);

        $task = $this->taskService->updateTaskStatus($taskId, $validated);
        $user = $this->taskService->getUserById($validated['user_id']);

        Mail::to($user['email'])->queue(new SharedTask($task, Auth::user(), $validated['status']));


        return jsonResponse([]);
        //return jsonResponse(['task' => $task, 'user' => $user, 'userAutenticated' => Auth::user()]);
    }

    public function historyTaskId($taskId)
    {
        $task = $this->taskService->getTaskHistory($taskId);

        return jsonResponse(['task' => $task,]);
    }

    public function getAllTasksWithLastStatus()
    {
        $tasks = $this->taskService->getAllTasksWithLastStatus();


        return jsonResponse(['task' => $tasks,]);
    }

    public function updateTask(UpdateTaskRequest $request, $taskId)
    {
        $validatedData = $request->validated();

        $task = $this->taskService->updateTask($taskId, $validatedData);

        return jsonResponse(['task' => $task,]);
    }

    public function deleteTask($taskId)
    {
        $this->taskService->deleteTask($taskId);

        return jsonResponse([]);
    }
}
