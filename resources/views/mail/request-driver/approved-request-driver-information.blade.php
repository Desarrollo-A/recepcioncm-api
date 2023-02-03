@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha y horario:
@component('mail::panel')
    Día: {{ $startDate }} a las {{$startTime}} 
        hasta el día {{$endDate}} a las {{$endTime}}.<br>
    Vehículo: {{$car}}.<br>
    Lugar de salida: {{$pickupState}}.<br>
    Lugar de llegada: {{$arrivalState}}.

@endcomponent

Saludos,<br>
{{ config('app.name') }}
@endcomponent
