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

    Route::prefix('request-packages')
        ->name('request-packages.')
        ->group(function () {
            Route::get('/completed/{requestPackageId}', 'RequestPackageController@isPackageCompleted')
                ->name('completed')
                ->where('requestPackageId', Validation::INTEGER_ID);

            Route::get('auth-code/{authCodePackage}', 'RequestPackageController@isAuthPackage')
                ->name('auth.code');

            Route::get('/show/{packageId}', 'RequestPackageController@showPackage')
                ->name('show-package')
                ->where('packageId', Validation::INTEGER_ID);

            Route::post('/insert-score', 'RequestPackageController@insertScore')
                ->name('insert.score');
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
            
                Route::get('/available-driver/{driverId}', 'CarController@findAllAvailableByDriverId')
                    ->name('show')
                    ->where('driverId', Validation::INTEGER_ID);

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
                Route::get('/update-snack-countable', 'InventoryRequestController@updateSnackCountable')
                    ->name('update.snack.countable');

                Route::get('/update-snack-uncountable', 'InventoryRequestController@updateSnackUncountable')
                    ->name('update.snack.uncountable');

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
                Route::get('/confirm-request','NotificationController@confirmRequest')
                    ->name('confirm.request');

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
                Route::get('/expired', 'RequestController@expiredRequest')
                    ->name('expired');

                Route::get('/finished', 'RequestController@finishedRequest')
                    ->name('finished');
                    
                Route::post('/rating', 'RequestController@starRatingRequest')
                    ->name('rating');

                Route::patch('/response-reject/{id}', 'RequestController@responseRejectRequest')
                    ->name('response-reject')
                    ->where('id', Validation::INTEGER_ID);

                Route::delete('/room/{id}', 'RequestController@deleteRequestRoom')
                    ->name('delete-request-room')
                    ->where('id', Validation::INTEGER_ID);

                Route::delete('/package/{id}', 'RequestController@deleteRequestPackage')
                    ->name('delete-request-package')
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

        Route::prefix('home')
            ->name('home.')
            ->group(function () {
                Route::get('/', 'HomeController@getAllDataHome')
                    ->name('index');
            });

        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {
                Route::get('/input-output', 'InputOutputInventoryViewController@findAllPaginated')
                    ->name('find-all-paginated');

                Route::get('/input-output/pdf', 'InputOutputInventoryViewController@getReportPdf')
                    ->name('report-pdf');

                Route::get('/input-output/excel', 'InputOutputInventoryViewController@getReportExcel')
                    ->name('report-excel');
            });
        
        Route::prefix('offices')
            ->name('offices.')
            ->group(function(){
                Route::get('/state-driver/{stateId}', 'OfficeController@getOfficeByStateWithDriver')
                    ->name('state-driver')
                    ->where('stateId', Validation::INTEGER_ID);

                Route::get('/state-driver-car/{stateId}/{noPeople}', 'OfficeController@getOfficeByStateWithDriverAndCar')
                    ->name('state-driver-car')
                    ->where('stateId', Validation::INTEGER_ID)
                    ->where('noPeople', Validation::INTEGER_ID);

                Route::get('/state-car/{stateId}/{noPeople}', 'OfficeController@getOfficeByStateWithCar')
                    ->name('state-car')
                    ->where('stateId', Validation::INTEGER_ID)
                    ->where('noPeople', Validation::INTEGER_ID);

                Route::get('/state-driver-whitout-office/{officeId}', 'OfficeController@getByStateWithDriverWithoutOffice')
                    ->name('state-driver-whitout-office')
                    ->where('officeId', Validation::INTEGER_ID);
            });
        
        Route::prefix('drivers')
            ->name('drivers.')
            ->group(function() {
                Route::get('/{id}', 'DriverController@show')
                    ->name('show')
                    ->where('id', Validation::INTEGER_ID);

                Route::get('/find-all-office', 'DriverController@findAllByOfficeId')
                    ->name('find-all-office');

                Route::get('/available-package/{officeId}/{date}', 'DriverController@getAvailableDriversPackage')
                    ->name('find-all-car-relation')
                    ->where('officeId', Validation::INTEGER_ID)
                    ->where('date', Validation::DATE_REGEX);

                Route::post('/car', 'DriverController@insertDriverCar')
                    ->name('car');
        });

        Route::prefix('request-packages')
            ->name('request-packages.')
            ->group(function () {
                Route::get('/{requestId}', 'RequestPackageController@show')
                    ->name('show')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::get('/schedule-drivers/{officeId}', 'RequestPackageController@getDriverSchedule')
                    ->name('schedule-drivers')
                    ->where('officeId', Validation::INTEGER_ID);

                Route::get('/status/{code}', 'RequestPackageController@getStatusByStatusCurrent')
                    ->name('status-by-status-current');

                Route::get('/driver/{driverId}/{date}', 'RequestPackageController@getPackagesByDriverId')
                    ->name('driver')
                    ->where('driverId', Validation::INTEGER_ID)
                    ->where('date', Validation::DATE_REGEX);

                Route::post('/approved', 'RequestPackageController@approvedRequestPackage')
                    ->name('approved');

                Route::put('/upload-file/{requestId}', 'RequestPackageController@uploadAuthorizationFile')
                    ->name('upload-file')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::patch('/cancel/{requestId}', 'RequestPackageController@cancelRequest')
                    ->name('cancel-request-package')
                    ->where('requestId', Validation::INTEGER_ID);

                Route::patch('/transfer/{packageId}', 'RequestPackageController@transferRequest')
                    ->name('transfer')
                    ->where('packageId', Validation::INTEGER_ID);

                Route::patch('/road/{requestId}', 'RequestPackageController@onRoadPackage')
                    ->name('road')
                    ->where('requestId', Validation::INTEGER_ID);
            });

        Route::prefix('request-drivers')
            ->name('request-drivers.')
            ->group(function () {
                Route::put('/upload-file/{requestId}', 'RequestDriverController@uploadAuthorizationFile')
                    ->name('upload-file')
                    ->where('requestId', Validation::INTEGER_ID);
            });

        Route::prefix('request-cars')
            ->name('request-cars.')
            ->group(function () {
                Route::put('/upload-file/{requestId}', 'RequestCarController@uploadAuthorizationFile')
                    ->name('upload-file')
                    ->where('requestId', Validation::INTEGER_ID);
                
                Route::delete('/{requestCarId}', 'RequestCarController@deleteRequestCar')
                    ->name('delete')
                    ->where('reuqestCarId', validation::INTEGER_ID);
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
        Route::apiResource('request-emails', 'RequestEmailController')->only('store', 'update', 'destroy');
        Route::apiResource('drivers', 'DriverController')->only('index');
        Route::apiResource('request-packages', 'RequestPackageController')->only('index', 'store');
        Route::apiResource('request-drivers', 'RequestDriverController')->only('store');
        Route::apiResource('request-cars', 'RequestCarController')->only('store');
    });
});