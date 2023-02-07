
@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha y horario:
@component('mail::panel')
    Día: {{ $startDate }} a las {{$startTime}} 
        hasta el día {{$endDate}} a las {{$endTime}}.<br>
    Vehículo: {{$car}}.<br>

@endcomponent

<strong>Motivo de cancelación:</strong> {{$comment}}

Saludos,<br>
{{ config('app.name') }}
@endcomponent
