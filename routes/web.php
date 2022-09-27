<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Api\UserController@loadPdf');
