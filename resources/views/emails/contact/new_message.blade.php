<!DOCTYPE html>
<html>

<head>
    <title>Nuevo Mensaje de Contacto</title>
</head>

<body>
    <h1>Nuevo mensaje de contacto</h1>
    <p>Has recibido un nuevo mensaje a través del formulario de contacto.</p>

    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <p><strong>De:</strong> {{ $contactMessage->name }} ({{ $contactMessage->email }})</p>
        @if($contactMessage->phone)
            <p><strong>Teléfono:</strong> {{ $contactMessage->phone }}</p>
        @endif
        <p><strong>Asunto:</strong> {{ $contactMessage->subject }}</p>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 10px 0;">
        <p><strong>Mensaje:</strong></p>
        <p style="white-space: pre-line;">{{ $contactMessage->message }}</p>
    </div>

    <p>
        <a href="{{ route('admin.messages.show', $contactMessage->id) }}"
            style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Responder en el Panel
        </a>
    </p>
</body>

</html>