<?php

use App\Http\Controllers\api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


require __DIR__.'/auth.php';
