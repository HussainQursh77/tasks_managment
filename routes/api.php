<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);

});

Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});


Route::apiResource('projects', ProjectController::class);
Route::post('projects/{project}/sync-users', [ProjectController::class, 'syncUsers']);
Route::post('projects/{project}/attach-user', [ProjectController::class, 'attachUser']);
Route::delete('projects/{project}/detach-user', [ProjectController::class, 'detachUser']);
Route::post('projects/{projectId}/start-work', [ProjectController::class, 'startWork']);
Route::post('projects/{projectId}/stop-work', [ProjectController::class, 'stopWork']);


