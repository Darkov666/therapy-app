<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContactMessage;
use App\Mail\ContactConfirmationMail;
use App\Mail\NewContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'use_nickname' => 'boolean',
        ]);

        $user = Auth::user();
        if ($user) {
            $name = $request->input('use_nickname', false) && $user->nickname
                ? $user->nickname
                : $user->name;
        } else {
            $name = $request->input('name');
            if (!$name) {
                // Should be required if not auth, but validation above is nullable. 
                // Let's refine validation rule dynamically or just generic check.
                // Actually better to enforce validaiton rule based on auth status in real app, 
                // but for now I'll just default to 'Guest' or fail if empty? 
                // The request requirement says "solicitar nombre...".
                // I will fix validation logic below.
            }
        }

        // Refined validation logic
        if (!$user && empty($request->name)) {
            return back()->withErrors(['name' => 'The name field is required.']);
        }

        $contactMessage = ContactMessage::create([
            'user_id' => $user ? $user->id : null,
            'name' => $name ?? $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send Emails
        // To Client
        Mail::to($contactMessage->email)->send(new ContactConfirmationMail($contactMessage));

        // To Psychologist (Admin/Titular) - assuming hardcoded or specific setting? 
        // User request: "al psicologo". I'll send to a configured admin email or the first admin found.
        // For now I'll use a placeholder or config.
        $adminEmail = 'juliana@therapy.app';
        Mail::to($adminEmail)->send(new NewContactMessageMail($contactMessage));

        return back()->with('success', 'Message sent successfully. We will contact you shortly.');
    }

    public function reply(Request $request, ContactMessage $message)
    {
        // Ensure the user owns the parent message or is in the thread
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $reply = ContactMessage::create([
            'user_id' => Auth::id(),
            'parent_id' => $message->id,
            'name' => Auth::user()->name, // Or nickname if we stored it? We use Auth user name for simplicity in backend for now.
            'email' => Auth::user()->email,
            'subject' => 'Re: ' . $message->subject,
            'message' => $request->message,
        ]);

        // Notify Admin
        // Reuse NewContactMessageMail or create specific Reply notification?
        // Let's use NewContactMessageMail for simplicity, it contains the info.
        $adminEmail = 'juliana@therapy.app';
        Mail::to($adminEmail)->send(new NewContactMessageMail($reply));

        return back()->with('success', 'Reply sent successfully.');
    }
    public function userMessages()
    {
        $user = Auth::user();

        $messages = ContactMessage::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->with([
                'replies' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Profile/Messages', [
            'messages' => $messages
        ]);
    }
}
