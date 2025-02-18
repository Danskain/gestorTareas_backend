<?php

use App\Http\Controllers\LigaController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/dashboard', [UsersController::class, 'dashboard']);
Route::get('/logout', [UsersController::class, 'logout']);


Route::get('/users', [UsersController::class, 'getAllUsers']);



Route::middleware('token')->group(function () {
    Route::post('/create-tasks', [TaskController::class, 'storeTask']);
    Route::put('/tasks/{taskId}', [TaskController::class, 'updateTask']);
    Route::delete('/tasks/{taskId}', [TaskController::class, 'deleteTask']);
    Route::patch('/tasks/{taskId}/update-status', [TaskController::class, 'updateTaskStatus']);
    Route::get('/tasks/last-status', [TaskController::class, 'getAllTasksWithLastStatus']);
    Route::get('/historyTask/{taskId}', [TaskController::class, 'historyTaskId']);

    Route::post('/ligas/{id}/inscribir', [LigaController::class, 'inscribirEquipo']); 
    Route::get('/equiposAll', [LigaController::class, 'getEquiposAll']); 
    Route::get('/ligas/{id}/equipos', [LigaController::class, 'showEquipos']); 
    Route::get('/ligasAll', [LigaController::class, 'getAllLigas']); 
    
    Route::middleware('rol:administrador')->group(function () {
        Route::get('/ligasAllUser', [LigaController::class, 'index']); 
        Route::post('/createLiga', [LigaController::class, 'store']); // Crear una nueva liga   
        Route::post('/fixture', [LigaController::class, 'generarFixture']); // Crear el Fiure  
    });
});
