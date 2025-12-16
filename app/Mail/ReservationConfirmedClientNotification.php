<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmedClientNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build()
    {
        $email = $this->subject('Reserva Confirmada - ' . $this->appointment->service->title)
            ->view('emails.client.booking_confirmed');

        if ($this->appointment->service->downloadable) {
            $filePath = $this->appointment->service->file_path;
            if (file_exists($filePath)) {
                $email->attach($filePath, [
                    'as' => $this->appointment->service->title . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $email;
    }
}
