@component('mail::message')
Buen día.

Te informamos que la solicitud de paquetería {{ $code }} ha sido <strong>CANCELADA</strong>.

Saludos,<br>
{{ config('app.name') }}
@endcomponent