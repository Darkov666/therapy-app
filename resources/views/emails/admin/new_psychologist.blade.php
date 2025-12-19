@component('mail::message')
# Nuevo Psicólogo Registrado

Se ha registrado un nuevo psicólogo en la plataforma y requiere aprobación.

**Nombre:** {{ $psychologist->name }}
**Email:** {{ $psychologist->email }}
**Teléfono:** {{ $psychologist->phone }}
**Fecha de Registro:** {{ $psychologist->created_at->format('d/m/Y H:i') }}

@component('mail::button', ['url' => route('admin.users.index', ['search' => $psychologist->email])])
Verificar Usuario
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent