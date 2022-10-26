<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\Validation;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')
        ->name('auth.')
        ->group(function () {
            Route::post('/login', 'AuthController@login')->name('login');
            Route::post('/restore-password', 'AuthController@restorePassword')->name('restore-password');
        });

    Route::apiResource('users', 'UserController')->only(['store']);

    // Rutas con autenticación
    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')
            ->name('auth.')
            ->group(function () {
                Route::get('/navigation', 'AuthController@getNavigationMenu')->name('navigation');
                Route::get('/user', 'AuthController@getUser')->name('user');
                Route::get('/logout', 'AuthController@logout')->name('logout');

                Route::post('/change-password', 'AuthController@changePassword')->name('change-password');
                Route::post('/pusher', 'AuthController@pusherAuth')->name('pusher');
            });

        Route::prefix('rooms')
            ->name('rooms.')
            ->group(function () {
                Route::get('/{id}', 'RoomController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::get('/find-all-state/{stateId}', 'RoomController@findAllByStateId')
                    ->name('find-all-state')
                    ->where('stateId', Validation::INTEGER_ID);

                Route::patch('/change-status/{id}', 'RoomController@changeStatus')
                    ->name('change-status')
                    ->where('id', Validation::INTEGER_ID);
            });

        Route::prefix('lookups')
            ->name('lookups.')
            ->group(function () {
                Route::get('/find-all-type/{type}', 'LookupController@findAllByType')
                    ->name('find-all-type')
                    ->where('type', Validation::INTEGER_ID);
            });

        Route::prefix('states')
            ->name('states.')
            ->group(function () {
                Route::get('/get-all', 'StateController@getAll')->name('get-all');
            });

        Route::prefix('inventories')
            ->name('inventories.')
            ->group(function () {
                Route::get('/{id}', 'InventoryController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::get('/coffee', 'InventoryController@findAllCoffee')
                    ->name('coffee');

                Route::put('/image/{id}', 'InventoryController@updateImage')
                    ->name('update-image')
                    ->where('id', Validation::INTEGER_ID);

                Route::patch('/stock/{id}', 'InventoryController@updateStock')
                    ->name('update-stock')
                    ->where('id', Validation::INTEGER_ID);

                Route::delete('/image/{id}', 'InventoryController@deleteImage')
                    ->name('delete.image')
                    ->where('id', Validation::INTEGER_ID);
            });

        Route::prefix('cars')
            ->name('cars.')
            ->group(function () {
                Route::get('/{id}', 'CarController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::patch('/change-status/{id}', 'CarController@changeStatus')
                    ->name('change-status')
                    ->where('id', Validation::INTEGER_ID);
            });

        Route::prefix('request-rooms')
            ->name('request-rooms.')
            ->group(function () {
                Route::get('/{id}', 'RequestRoomController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::get('/status/{code}', 'RequestRoomController@getStatusByStatusCurrent')
                    ->name('status-by-status-current');

                Route::get('/schedule/{requestId}/{date}', 'RequestRoomController@getAvailableScheduleByDay')
                    ->name('schedule')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::post('/assign-snack', 'RequestRoomController@assignSnack')
                    ->name('assign-snack');

                Route::post('/available-room', 'RequestRoomController@isAvailableSchedule')
                    ->name('available-room');

                Route::patch('/cancel/{requestId}', 'RequestRoomController@cancelRequest')
                    ->name('cancel-request-room')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::patch('/proposal/{requestId}', 'RequestRoomController@proposalRequest')
                    ->name('proposal-request-room')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::patch('/without-attending/{requestId}', 'RequestRoomController@withoutAttendingRequest')
                    ->name('without-attending-request-room')
                    ->where('requestId', Validation::INTEGER_ID);
            });

        Route::prefix('inventory-request')
            ->name('inventory-request.')
            ->group(function () {
                Route::post('/', 'InventoryRequestController@store')
                    ->name('store');

                Route::put('/{requestId}/{inventoryId}', 'InventoryRequestController@update')
                    ->name('update')
                    ->where('requestId', Validation::INTEGER_ID)
                    ->where('inventoryId', Validation::INTEGER_ID);

                Route::delete('/{requestId}/{inventoryId}', 'InventoryRequestController@delete')
                    ->name('delete')
                    ->where('requestId', Validation::INTEGER_ID)
                    ->where('inventoryId', Validation::INTEGER_ID);
            });

        Route::prefix('notifications')
            ->name('notifications.')
            ->group(function (){
                Route::get('/last','NotificationController@getAllNotificationLast5Days')
                    ->name('last');

                Route::patch('/read/{id}', 'NotificationController@readNotification')
                    ->name('read')
                    ->where('id', Validation::INTEGER_ID);

                Route::patch('/read-all', 'NotificationController@readAllNotification')
                    ->name('read-all')
                    ->where('id', Validation::INTEGER_ID);

                Route::patch('/answered-notification/{notificationId}', 'NotificationController@wasAnswered')
                    ->name('answered-notification')
                    ->where('notificationId', Validation::INTEGER_ID);
            });

        Route::prefix('requests')
            ->name('requests.')
            ->group(function () {
                Route::patch('/response-reject/{id}', 'RequestController@responseRejectRequest')
                    ->name('response-reject')
                    ->where('id', Validation::INTEGER_ID);

                Route::delete('/room/{id}', 'RequestController@deleteRequestRoom')
                    ->name('delete-request-room')
                    ->where('id', Validation::INTEGER_ID);
            });

        Route::prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/{id}', 'UserController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::get('/profile', 'UserController@showProfile')
                    ->name('profile');

                Route::patch('/change-status/{id}', 'UserController@changeStatus')
                    ->name('change-status')
                    ->where('id', Validation::INTEGER_ID);
            });

        Route::prefix('calendar')
            ->name('calendar.')
            ->group(function () {
                Route::get('/', 'CalendarController@findAll')
                    ->name('find-all');
                Route::get('/summary-day', 'CalendarController@getSummaryOfDay')
                    ->name('summary-day');
            });

        Route::prefix('report')
            ->name('report.')
            ->group(function () {
                Route::get('/input-output', 'InputOutputInventoryViewController@findAllPaginated')
                    ->name('find-all-paginated');

                Route::get('/input-output/pdf', 'InputOutputInventoryViewController@getReportPdf')
                    ->name('report.pdf');

                Route::get('/input-output/excel', 'InputOutputInventoryViewController@getReportExcel')
                    ->name('report.excel');
            });

        Route::apiResource('cars', 'CarController')->only('store', 'index', 'update', 'destroy');
        Route::apiResource('rooms', 'RoomController')->only('store', 'index', 'update', 'destroy');
        Route::apiResource('request-rooms', 'RequestRoomController')->only('store', 'index');
        Route::apiResource('inventories', 'InventoryController')->only('store', 'index',
            'update', 'destroy');
        Route::apiResource('users', 'UserController')->only('index');
        Route::apiResource('requests', 'RequestController')->only('show');
        Route::apiResource('request-phone-numbers', 'RequestPhoneNumberController')
            ->only('store', 'update', 'destroy');
        Route::apiResource('notifications', 'NotificationController')->only('show');
    });
});