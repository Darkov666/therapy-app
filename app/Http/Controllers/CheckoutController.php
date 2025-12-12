<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();

        $cart = Cart::with('items.service')->where('user_id', $user ? $user->id : null)
            ->orWhere('session_id', $sessionId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // Check if scheduling is required
        $requiresScheduling = $cart->items->contains(fn($item) => $item->service->requires_scheduling);
        $appointments = session('booking_appointments', []);

        if ($requiresScheduling && empty($appointments)) {
            return redirect()->route('scheduling.index');
        }

        return Inertia::render('Shop/Checkout', [
            'cart' => $cart,
            'appointments' => $appointments,
            'user' => $user
        ]);
    }

    private function getCart(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }
        $sessionId = $request->session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,transfer',
            'name' => 'required_without:user_id|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required_without:user_id|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|string',
            'session_type' => 'required|in:online,in_person',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Handle User Auto-creation or Retrieval if not logged in
        if (!$user) {
            // Mimicking BookingController behavior: Find or Create
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name . ($request->surname ? ' ' . $request->surname : ''),
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)), // Random password
                    'role' => 'patient',
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                ]
            );

            // If user existed, maybe update phone/gender if missing? 
            // For now, respect existing data if found.

            Auth::login($user);

            // Migrate Cart
            $sessionId = $request->session()->getId();
            Cart::where('session_id', $sessionId)->update(['user_id' => $user->id, 'session_id' => null]);
        }

        // Handle Profile Photo Upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Re-fetch cart for the user
        $cart = Cart::with('items.service')->where('user_id', $user->id)->first();

        if (!$cart) {
            return redirect('/')->with('error', 'Carrito no encontrado o expirado.');
        }

        $appointmentsData = session('booking_appointments', []);
        $paymentMethod = $request->payment_method;
        $appointmentStatus = ($paymentMethod === 'transfer') ? 'pending' : 'confirmed';

        $createdAppointments = []; // Collect for emails

        foreach ($cart->items as $item) {
            if ($item->service->requires_scheduling) {
                if (isset($appointmentsData[$item->id])) {
                    $data = $appointmentsData[$item->id];

                    $appt = Appointment::create([
                        'user_id' => $user->id,
                        'service_id' => $item->service_id,
                        'scheduled_at' => $data['date'] . ' ' . $data['time'],
                        'end_time' => \Carbon\Carbon::parse($data['date'] . ' ' . $data['time'])->addMinutes($item->service->duration_minutes),
                        'status' => $appointmentStatus,
                        'payment_method' => $paymentMethod,
                        'payment_status' => ($appointmentStatus === 'confirmed') ? 'paid' : 'pending',
                        'customer_Name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => $request->phone ?? $user->phone,
                        'session_type' => $request->session_type, // Saved from checkout form
                        'token' => \Illuminate\Support\Str::uuid(),
                        'expires_at' => now()->addHours(24),
                    ]);
                    $createdAppointments[] = $appt;
                }
            } else {
                // Non-scheduled items logic (future)
            }
        }

        // Clear cart and session
        $cart->items()->delete();
        $cart->delete();
        session()->forget('booking_appointments');

        // Send Emails
        if ($paymentMethod === 'transfer') {
            foreach ($createdAppointments as $appt) {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\BookingPendingClientNotification($appt));
                \Illuminate\Support\Facades\Mail::to('terapia.test@gmail.com')->send(new \App\Mail\NewBookingAdminNotification($appt));
            }
        } elseif ($paymentMethod === 'paypal') {
            foreach ($createdAppointments as $appt) {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReservationConfirmedClientNotification($appt));
                \Illuminate\Support\Facades\Mail::to('terapia.test@gmail.com')->send(new \App\Mail\NewBookingAdminNotification($appt));
            }
        }

        $message = 'Compra realizada con éxito. ' . ($paymentMethod == 'transfer' ? 'Esperando confirmación de transferencia. Revisa tu correo.' : 'Pago confirmado. Revisa tu correo.');
        return redirect('/')->with('success', $message);
    }
}
