@component('mail::message')
Hola {{ $fullName }}

@component('mail::panel')
Tu nueva contraseña es: <b>{{ $newPassword }}</b>
@endcomponent

Gracias,<br>
{{ config('app.name') }}

@component('mail::subcopy')
Nota: Te recordamos que puedes cambiar tu contraseña en tu perfil dentro de la plataforma.
@endcomponent
@endcomponent