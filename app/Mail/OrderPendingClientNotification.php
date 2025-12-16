<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPendingClientNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointments;
    public $total;

    public function __construct($appointments)
    {
        $this->appointments = $appointments;
        // Calculate total 
        $this->total = 0;
        foreach ($appointments as $appt) {
            // Assuming service relationship is loaded
            $this->total += $appt->service->price_mxn;
        }
    }

    public function build()
    {
        return $this->subject('Solicitud de Compra Recibida (Pendiente de Pago)')
            ->view('emails.client.order_pending');
    }
}
