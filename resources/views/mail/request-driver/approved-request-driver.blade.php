@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha y horario:
@component('mail::panel')
    Día {{ $startDate }} a las {{ $startTime }} hasta el día {{$endDate}} a las {{ $endTime }}.<br>
    Chofer: {{$driver}}.<br>
    Vehículo: {{$car}}.
@endcomponent

Saludos,<br>
{{ config('app.name') }}
@endcomponent
