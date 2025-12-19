@component('mail::message')
# Registro Recibido

Hola {{ $psychologist->name }},

Hemos recibido tu solicitud de registro como Psicólogo en **TherapyApp**.

Tu cuenta se encuentra actualmente en proceso de revisión y aprobación por parte de nuestro equipo de administración. Te
notificaremos vía correo electrónico una vez que tu cuenta haya sido activada.

Este proceso nos ayuda a garantizar la calidad y seguridad de los profesionales en nuestra plataforma.

Gracias por tu paciencia,<br>
El equipo de {{ config('app.name') }}
@endcomponent