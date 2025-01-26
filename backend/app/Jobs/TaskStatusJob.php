<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SharedTask;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskStatusJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $taskId;
    public $validated;
    protected $taskService;
    //public $tries = 5;


    /**
     * Create a new job instance.
     */
    public function __construct($taskService, $taskId, $validated)
    {
        //
        $this->taskId = $taskId;
        $this->validated =  $validated;
        $this->taskService = $taskService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $task = $this->taskService->updateTaskStatus($this->taskId, $this->validated);
        $user = $this->taskService->getUserById($this->validated['user_id']);

        //Mail::to($user->email)->send(new SharedTask($task, Auth::user(), $this->validated['user_id']));

    }
}
