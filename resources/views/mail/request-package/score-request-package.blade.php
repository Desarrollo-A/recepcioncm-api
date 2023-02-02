@component('mail::message')
Hola {{ $fullName }}

Califica el envío de tu paquete en el siguiente enlace.

@component('mail::button', ['url' => $url])
    CLIC AQUÍ
@endcomponent

Gracias,<br>
{{ config('app.name') }}

@endcomponent