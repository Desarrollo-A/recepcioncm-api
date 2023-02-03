@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha:
@component('mail::panel')
    Día: {{ $date }}.<br>
    Vehículo: {{$car}}.<br>
    Lugar de salida: {{$pickupState}}.<br>
    Lugar de llegada: {{$arrivalState}}.

@endcomponent

Saludos,<br>
{{ config('app.name') }}
@endcomponent
