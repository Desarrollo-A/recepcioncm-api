@component('mail::message')
Hola {{ $fullName }}.

Se ha registrado correctamente tu usuario en el sistema de Administración de oficinas. Puedes visitar el sitio con el siguiente <a href="{{$urlFront}}">link</a>.

@component('mail::panel')
    <strong>Usuario:</strong> {{$code}}<br>
    <strong>Contraseña:</strong> {{ $password }}
@endcomponent

Saludos,<br>
{{ config('app.name') }}

@component('mail::subcopy')
    Nota: Te recordamos que puedes cambiar tu contraseña en tu perfil dentro de la plataforma.
@endcomponent
@endcomponent