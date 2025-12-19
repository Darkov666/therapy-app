@component('mail::message')
# ¡Cuenta Aprobada!

Hola {{ $psychologist->name }},

Nos complace informarte que tu cuenta de Psicólogo en **TherapyApp** ha sido aprobada y activada exitosamente.

Ya puedes iniciar sesión y comenzar a configurar tu perfil profesional, servicios y disponibilidad.

@component('mail::button', ['url' => url('/login')])
Iniciar Sesión
@endcomponent

---

## Términos y Condiciones de Uso

Al hacer uso de esta plataforma, usted acepta los siguientes términos y condiciones:

1. **Uso Profesional**: Usted se compromete a utilizar la plataforma exclusivamente para fines profesionales
relacionados con la psicología y la terapia.
2. **Veracidad de la Información**: Usted garantiza que toda la información proporcionada en su perfil y servicios es
veraz y actualizada.
3. **Conducta Ética**: Usted se compromete a mantener los más altos estándares éticos en su práctica profesional y en su
interacción con los clientes a través de la plataforma.
4. **Derecho de Cancelación**: La administración de TherapyApp se reserva el derecho de suspender o cancelar su cuenta
en cualquier momento, sin previo aviso, en caso de detectar mal uso de la plataforma, violación de estos términos, o
cualquier conducta que pueda perjudicar la reputación del servicio o la seguridad de los usuarios.

Si tiene alguna pregunta, no dude en contactarnos.

Atentamente,<br>
{{ config('app.name') }}
@endcomponent