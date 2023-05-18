@component('mail::message')
Buen día.

Te informamos que la solicitud de paquetería {{ $code }} ha sido <strong>APROBADA</strong> y tiene como fecha de llegada aproximada el día {{ $deliveryDate }}.

Saludos,<br>
{{ config('app.name') }}
@endcomponent
