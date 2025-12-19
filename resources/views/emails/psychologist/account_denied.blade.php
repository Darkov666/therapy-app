@component('mail::message')
# Estado de su Cuenta

Hola {{ $psychologist->name }},

Le informamos que tras revisar su solicitud de registro, su cuenta no ha sido aprobada en este momento.

Si considera que esto es un error o desea obtener más información sobre los motivos de esta decisión, por favor póngase
en contacto con nuestro equipo de soporte.

Gracias,<br>
{{ config('app.name') }}
@endcomponent