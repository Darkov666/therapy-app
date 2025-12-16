<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Aprobada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
            color: #333;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .icon {
            color: #4CAF50;
            font-size: 48px;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 24px;
        }

        p {
            margin-bottom: 30px;
            color: #666;
        }

        .btn {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>¡Operación Exitosa!</h1>
        <p>La reserva/compra ha sido aprobada y el cliente ha sido notificado por correo electrónico.</p>
        <button onclick="window.close()" class="btn">Cerrar Ventana</button>
    </div>
</body>

</html>