<!DOCTYPE html>
<html>

<head>
    <title>Resumen de Nuevas Reservas</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .appointment {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .header {
            background-color: #eee;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Nuevas Reservas / Compras Recibidas</h2>
            <p>Se han realizado múltiples transacciones en una sola orden.</p>
        </div>

        @foreach($appointments as $appointment)
            <div class="appointment">
                <h3>{{ $appointment->service->title }} ({{ $appointment->scheduled_at ? 'Reserva' : 'Compra' }})</h3>
                <p><strong>Cliente:</strong> {{ $appointment->customer_name }}</p>
                <p><strong>Email:</strong> {{ $appointment->customer_email }}</p>
                <p><strong>Teléfono:</strong> {{ $appointment->customer_phone }}</p>

                @if($appointment->service->downloadable)
                    <p><strong>Tipo:</strong> Producto Digital (Descargable)</p>
                    <p><strong>Estado:</strong> Pendiente de Pago/Aprobación</p>
                @elseif($appointment->scheduled_at)
                    <p><strong>Fecha Solicitada:</strong>
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Tipo:</strong> {{ ucfirst($appointment->session_type) }}</p>

                    @if($appointment->participants_data)
                        <p><strong>Participantes Adicionales:</strong></p>
                        <ul>
                            @foreach(json_decode($appointment->participants_data) as $p)
                                <li>{{ $p->name }} {{ $p->surname }} ({{ $p->gender }})</li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <p><strong>Estado:</strong> Fecha Pendiente (Open Date)</p>
                    <p>El cliente seleccionará la fecha desde su panel.</p>
                @endif

                <div style="margin-top: 15px;">
                    <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('booking.accept.signed', $appointment->id) }}"
                        class="btn">
                        {{ $appointment->service->downloadable ? 'Autorizar Envío' : 'Aceptar Reserva' }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>