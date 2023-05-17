@component('mail::message')
Buen día.

Te informamos que la solicitud de paquetería {{ $code }} está en camino.

Saludos,<br>
{{ config('app.name') }}
@endcomponent
