<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class ProfileController extends Controller
{
    public function show()
    {
        return Inertia::render('Profile/Show', [
            'sessions' => $this->getSessions(),
        ]);
    }

    public function destroySessions(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('The provided password does not match your current password.')],
            ]);
        }

        Auth::guard('web')->logoutOtherDevices($request->password);

        return back()->with('success', 'Todas las otras sesiones han sido cerradas.');
    }

    protected function getSessions()
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'agent' => $this->createAgent($session),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity, config('app.timezone', 'UTC'))->diffForHumans(),
                ];
            });
    }

    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
