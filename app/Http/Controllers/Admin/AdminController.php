<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Appointment;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Enhanced Dashboard metrics
        $metrics = [
            'total_users' => User::count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'confirmed_appointments' => Appointment::where('status', 'confirmed')->count(),
            'total_sales' => Appointment::where('payment_status', 'paid')->count(),
            'top_psychologists' => Appointment::select('psychologist_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->whereNotNull('psychologist_id')
                ->where('status', 'confirmed')
                ->groupBy('psychologist_id')
                ->with('psychologist')
                ->orderByDesc('total')
                ->take(5)
                ->get(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'metrics' => $metrics
        ]);
    }

    // User Management (Psychologists/Titular)
    public function users()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::whereIn('role', ['admin', 'titular'])->get()
        ]);
    }

    public function createUser()
    {
        return Inertia::render('Admin/Users/Create');
    }

    // Settings Management
    public function settings()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => \App\Models\Setting::all()
        ]);
    }

    public function updateSettings(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($data['settings'] as $item) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return redirect()->back()->with('success', 'Configuración actualizada.');
    }
}
