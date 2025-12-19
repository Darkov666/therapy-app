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
        $user = Auth::user();

        // Fetch Psychologists for Dropdown
        // Logic: All psychologists + Titulars
        $psychologists = \App\Models\User::whereIn('role', ['psychologist', 'titular'])
            ->select('id', 'name', 'nickname')
            ->get();

        $assignedPsychologistId = null;
        if ($user && $user->created_by) {
            $assignedPsychologistId = $user->created_by;
        }

        return Inertia::render('Contact', [
            'psychologists' => $psychologists,
            'assignedPsychologistId' => $assignedPsychologistId
        ]);
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
            'psychologist_id' => 'nullable|exists:users,id',
        ]);

        $user = Auth::user();
        if ($user) {
            $name = $request->input('use_nickname', false) && $user->nickname
                ? $user->nickname
                : $user->name;
        } else {
            $name = $request->input('name') ?? 'Guest';
        }

        if (!$user && empty($request->name)) {
            return back()->withErrors(['name' => 'The name field is required.']);
        }

        $contactMessage = ContactMessage::create([
            'user_id' => $user ? $user->id : null,
            'name' => $name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            // We could store the target psychologist_id in the message model if we added a migration, 
            // but for now we just route the email.
        ]);

        // Email Routing Logic
        $recipientEmail = 'juliana@therapy.app'; // Default Admin

        if ($request->filled('psychologist_id')) {
            $psychologist = \App\Models\User::find($request->psychologist_id);
            if ($psychologist) {
                $recipientEmail = $psychologist->email;
            }
        } elseif ($user && $user->creator) {
            // Fallback to assigned psychologist if logged in
            $recipientEmail = $user->creator->email;
        }
        // Logic for "Route Context" (subdomain/path) would go here if we had the context passed in request.
        // For now, defaulting to Admin if no selection and no creator.

        // Send Emails
        // To Client
        Mail::to($contactMessage->email)->send(new ContactConfirmationMail($contactMessage));

        // To Selected Psychologist / Admin
        Mail::to($recipientEmail)->send(new NewContactMessageMail($contactMessage));

        return back()->with('success', 'Message sent successfully.');
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
