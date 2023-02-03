@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha:
@component('mail::panel')
    Día: {{ $date }}.<br>
    Vehículo: {{$car}}.<br>

@endcomponent
<strong>Motivo de cancelación:</strong> {{$comment}}
    
Saludos,<br>
{{ config('app.name') }}
@endcomponent
