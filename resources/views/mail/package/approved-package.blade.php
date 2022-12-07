@component('mail::message')
Hola {{ $fullName }}

@component('mail::panel')
Por favor calificar tu solicitud de paquetería con código:
        <b>{{ $codeRequest }}</b> en el siguiente enlace.
    <br><a>{{$url}}</a>
@endcomponent

Gracias,<br>
{{ config('app.name') }}

@endcomponent