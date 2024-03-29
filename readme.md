<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Instalación del proyecto

Nota: Se debe tener instalado la versión de PHP 7.2 como mínimo, Composer y Node JS.

- Descargar el repositorio de git.
- Ejecutar el comando `composer install` para las dependiencias del proyecto.
- Ejecutar el comando `npm install` para las dependencias de Node del proyecto.
- Ejecutar el comando `php artisan storage:link` para agregar la carpeta pública de storage.

## Generar datos fake para pruebas

- Ejecutar el comando `php artisan migrate:fresh --seed`. Lo que hará es borrar todas las tablas de la BD, crearlas de nuevo y ejecutar los seeder para los datos fake de las tablas.

## Ejecutar el servidor
- Ejecutar el comando `php artisan serve` para levantar un servidor local en la dirección `http://127.0.0.1:8000`

## Documentación del proyecto

- Laravel 5.8 `https://laravel.com/docs/5.8`
- Laravel DOMPDF `https://github.com/barryvdh/laravel-dompdf/tree/v0.8.7`
- Intervention Image (Paquete para optimización de imágenes) `https://image.intervention.io/v2`
- Laravel CORS `https://github.com/fruitcake/laravel-cors/tree/v1.0.6`
- Laravel Pusher `https://pusher.com/`
- Fast Excel `https://github.com/rap2hpoutre/fast-excel/tree/v2.1.0`
- Laravel Google Calendar `https://github.com/spatie/laravel-google-calendar/tree/2.1.0`
