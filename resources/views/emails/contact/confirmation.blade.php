<!DOCTYPE html>
<html>

<head>
    <title>Confirmación de Contacto</title>
</head>

<body>
    <h1>¡Hemos recibido tu mensaje!</h1>
    <p>Hola {{ $contactMessage->name }},</p>
    <p>Gracias por ponerte en contacto con nosotros. Hemos recibido tu mensaje con el asunto:
        <strong>{{ $contactMessage->subject }}</strong></p>
    <p>Nuestro equipo revisará tu consulta y nos pondremos en contacto contigo a la brevedad posible.</p>
    <br>
    <p>Atentamente,</p>
    <p><strong>Equipo de Conecta Contigo</strong></p>
</body>

</html>