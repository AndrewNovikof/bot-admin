<?php

use App\Http\Controllers\BotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ScheduleEventController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->name('dashboard.')->prefix('dashboard')->group( function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
});

Route::middleware(['auth:sanctum', 'verified'])->name('bots.')->prefix('bots')->group(function () {
    Route::get('create', [BotController::class, 'create'])->name('create');
    Route::post('/', [BotController::class, 'store'])->name('store');
    Route::get('{bot}', [BotController::class, 'show'])->name('show');
    Route::put('{bot}', [BotController::class, 'update'])->name('update');
    Route::delete('{bot}', [BotController::class, 'destroy'])->name('destroy');
    Route::name('chats.')->prefix('{bot}/chats')->group(function() {
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::put('{chat}', [ChatController::class, 'update'])->name('update');
        Route::delete('{chat}', [ChatController::class, 'destroy'])->name('destroy');
        Route::delete('{chat}', [ChatController::class, 'call'])->name('call');
    });
    Route::name('messages.')->prefix('{bot}/messages')->group(function() {
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::put('{message}', [MessageController::class, 'update'])->name('update');
        Route::delete('{message}', [MessageController::class, 'destroy'])->name('destroy');
    });
    Route::name('scheduleEvents.')->prefix('{bot}/scheduleEvents')->group(function() {
        Route::post('/', [ScheduleEventController::class, 'store'])->name('store');
        Route::put('{scheduleEvent}', [ScheduleEventController::class, 'update'])->name('update');
        Route::delete('{scheduleEvent}', [ScheduleEventController::class, 'destroy'])->name('destroy');
        Route::post('{scheduleEvent}/call', [ScheduleEventController::class, 'call'])->name('call');
    });
});

