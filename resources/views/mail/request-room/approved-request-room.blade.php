@component('mail::message')
Buen día.

Te informamos que la solicitud {{ $code }} ha sido <strong>{{ $status }}</strong> en la siguiente fecha y horario:
@component('mail::panel')
    Día {{ $date }}, {{ $startTime }} a {{ $endTime }} en la oficina {{ $office }}, sala {{ $room }}.
@endcomponent

Saludos,<br>
{{ config('app.name') }}
@endcomponent