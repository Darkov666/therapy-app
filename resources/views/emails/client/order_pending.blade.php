<!DOCTYPE html>
<html>

<head>
    <title>Solicitud de Compra Recibida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: #f4f4f4;
        }

        .bank-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #ffeeba;
        }

        .btn-container {
            margin-top: 30px;
            text-align: center;
        }

        .btn {
            background-color: #25D366;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #2c3e50;">Tu Solicitud está Pendiente</h2>

        <p>Hola {{ $appointments[0]->customer_name }},</p>
        <p>Hemos recibido tu solicitud de compra/reserva para los siguientes servicios:</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Servicio / Producto</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                    <tr>
                        <td>
                            {{ $appt->service->title }}
                            @if($appt->scheduled_at)
                                <br><small>Reserva:
                                    {{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d/m/Y H:i') }}</small>
                            @endif
                        </td>
                        <td>{{ $appt->scheduled_at ? 'Reserva' : 'Producto' }}</td>
                        <td>${{ number_format($appt->service->price_mxn, 2) }} MXN</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>${{ number_format($total, 2) }} MXN</strong></td>
                </tr>
            </tbody>
        </table>

        <p>Para confirmar tu pedido, por favor realiza el pago mediante transferencia bancaria.</p>

        <div class="bank-details">
            <h3 style="margin-top: 0;">Datos Bancarios:</h3>
            <p><strong>Banco:</strong> BBVA</p>
            <p><strong>Cuenta:</strong> 1234567890</p>
            <p><strong>CLABE:</strong> 012345678901234567</p>
            <p><strong>Titular:</strong> Yuliana Andrea Sanchez Navarrete</p>
            <p><strong>Monto Total:</strong> ${{ number_format($total, 2) }} MXN</p>
        </div>

        <div class="warning">
            <strong>IMPORTANTE:</strong> Tienes 24 horas para enviar tu comprobante de pago, de lo contrario la
            solicitud se cancelará automáticamente.
        </div>

        <p>Por favor, envía tu comprobante vía WhatsApp haciendo clic en el siguiente botón:</p>

        <div class="btn-container">
            <a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}?text=Hola,%20envío%20comprobante%20para%20la%20orden%20pendiente%20a%20nombre%20de%20{{ urlencode($appointments[0]->customer_name) }}"
                class="btn">
                Enviar Comprobante por WhatsApp
            </a>
        </div>
    </div>
</body>

</html>