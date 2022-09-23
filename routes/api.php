<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')
        ->name('auth.')
        ->group(function () {
            Route::post('/login', 'AuthController@login')->name('login');
        });

    // Rutas con autenticación
    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')
            ->name('auth.')
            ->group(function () {
                Route::get('/user', 'AuthController@getUser')->name('user');
                Route::get('/logout', 'AuthController@logout')->name('logout');
            });
    });
});