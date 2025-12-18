<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmationMail; // Reuse or create new "Reply" mail? 
// Usually a reply is just an email back. 
// Requirement: "responder desde la misma aplicacion donde lee el correo".
// So we send an email to the user with the reply content.

class MessageController extends Controller
{
    public function index()
    {
        $this->checkPrivacy();

        $messages = ContactMessage::whereNull('parent_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages
        ]);
    }

    public function show(ContactMessage $message)
    {
        $this->checkPrivacy();

        // Load thread
        $message->load(['replies.user', 'replies.replies', 'user']);
        // Note: unlimited nesting might need recursive loading or flat map. 
        // For simplicity, let's assume 1 level of threaded view or just load all related by root parent?
        // Better: Fetch all messages with same root parent or just load children recursively.
        // Let's just load direct replies for now, or improve query if needed.
        // Actually, for a chat-like history, we might want to fetch all messages where root_id is X. 
        // But my DB design used adjacency list (parent_id).
        // Let's load replies recursively.

        // Mark as read
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return Inertia::render('Admin/Messages/Show', [
            'message' => $message
        ]);
    }

    public function store(Request $request, ContactMessage $message)
    {
        $this->checkPrivacy();

        $request->validate([
            'message' => 'required|string'
        ]);

        $reply = ContactMessage::create([
            'user_id' => Auth::id(),
            'parent_id' => $message->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'subject' => 'Re: ' . $message->subject,
            'message' => $request->message,
            'is_read' => true, // Self-read
        ]);

        // Send email to original sender (the user)
        // We need to send to $message->email (the client)
        // We should create a ReplyMail mailable.
        // For now, I'll use raw mail or standard notification.
        // Let's assume I need to create a ReplyMail or just generic.
        // Requirement: "responder... y que se pueda observar el historial".
        // The reply is stored in DB. I should also email the user.

        // To implement email sending later or now? I'll add a TODO or basic raw mail.
        // "el correo de notificacion debe llegar al cliente...". 
        // This was for the initial contact. For replies, it implies communication.
        // I will implement a basic Mail::send or create a generic class. 
        // Let's stick to storing for now and assume email reply is handled.
        // Actually, if I don't email them, they won't know.
        // I'll send a simple raw email for now to unblock.

        Mail::raw($request->message, function ($mail) use ($message) {
            $mail->to($message->email)
                ->subject('Re: ' . $message->subject);
        });

        return back()->with('success', 'Reply sent successfully.');
    }

    protected function checkPrivacy()
    {
        if (Auth::user()->role === 'root') {
            abort(403, 'This area is restricted for privacy reasons.');
        }
    }
}
