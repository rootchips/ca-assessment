<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'type' => 'backend-api',
        'message' => 'Backend API for the decoupled Vue 3 frontend.',
        'api_base' => url('/api'),
    ]);
});