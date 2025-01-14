<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/dashboard', [UsersController::class, 'dashboard']);
Route::get('/logout', [UsersController::class, 'logout']);
Route::get('/users', [UsersController::class, 'getAllUsers']);

Route::post('/create-tasks', [TaskController::class, 'storeTask']);
Route::put('/tasks/{taskId}', [TaskController::class, 'updateTask']);
Route::delete('/tasks/{taskId}', [TaskController::class, 'deleteTask']);
Route::patch('/tasks/{taskId}/update-status', [TaskController::class, 'updateTaskStatus']);
Route::get('/tasks/last-status', [TaskController::class, 'getAllTasksWithLastStatus']);
Route::get('/historyTask/{taskId}', [TaskController::class, 'historyTaskId']);
