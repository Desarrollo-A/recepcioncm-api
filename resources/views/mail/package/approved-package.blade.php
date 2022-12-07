@component('mail::message')
Hola {{ $fullName }}

Para terminar el proceso, visita el siguiente enlace una vez ya recibido el paquete.

@component('mail::button', ['url' => $url])
    CLIC AQUÍ
@endcomponent

Gracias,<br>
{{ config('app.name') }}

@endcomponent