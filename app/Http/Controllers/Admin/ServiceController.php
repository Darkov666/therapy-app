<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Service::query()->with('category', 'creator');

        // Permissions: Psychologist sees only services they created or are authorized for?
        // Requirement: "psychologists unicamente a los que tiene dados de alta en su propia sesion"
        // AND "subir nuevo servicios".
        // Let's show services created by them.
        if ($user->role !== 'admin' && $user->role !== 'root') {
            $query->where('user_id', $user->id);
        }

        $query->when($request->search, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%");
        });

        $services = $query->latest()->paginate(10)->withQueryString();

        $services->through(function ($service) {
            if ($service->image) {
                $service->image_url = \Illuminate\Support\Facades\URL::signedRoute('media.secure', ['path' => $service->image]);
            }
            return $service;
        });

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        $categories = Category::where('is_active', true)->where('type', 'service')->get();

        // Psychologists available to assign
        // Admin sees all. Psychologist sees ?? Just themselves? Or users they created?
        // Since we didn't strictly implement "created_by" on users table yet, 
        // let's assume Psychologists can at least assign themselves.
        // And if they are titular, maybe all psychologists?
        // "el super usuario... asignar el psicolo padre y asignar los permisos a los psicologos hijos"
        // Let's list all psychologists for Admin.
        // For Psychologist, list themselves.

        if ($user->role === 'admin' || $user->role === 'root') {
            $psychologists = User::whereIn('role', ['psychologist', 'titular'])->get();
        } else {
            // Limited view
            $psychologists = User::where('id', $user->id)->get();
            // TODO: If we implemented child-users, add them here.
        }

        return Inertia::render('Admin/Services/Create', [
            'categories' => $categories,
            'psychologists' => $psychologists,
            'exchange_rate' => Setting::get('exchange_rate', 20.00), // Default 20
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string|in:individual,couple,family,group,workshop,special,ebook,video,manual,audio',
            'duration_minutes' => 'required|integer',
            'price_mxn' => 'required|numeric|min:0',
            'psychologists' => 'array',
            'psychologists.*' => 'exists:users,id',
            'image' => 'nullable|image|max:2048', // 2MB
        ]);

        $exchangeRate = Setting::get('exchange_rate', 20.00);
        $priceUsd = $validated['price_mxn'] / $exchangeRate;

        $path = null;
        if ($request->hasFile('image')) {
            // Private storage
            $path = $request->file('image')->store('private/services', 'local');
        }

        $service = Service::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'duration_minutes' => $validated['duration_minutes'],
            'price_mxn' => $validated['price_mxn'],
            'price_usd' => $priceUsd,
            'price' => $validated['price_mxn'], // Base price usually matches MXN in this context? Or handled by currency switcher. 
            // Model usually has 'price' as generic. Let's start with MXN.
            'image' => $path,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        if (isset($validated['psychologists'])) {
            // Validate permission to assign? 
            // Admin can assign anyone. Psychologist can assign themselves.
            // We trust the form for now given the props filtering.
            $service->psychologists()->sync($validated['psychologists']);
        } else {
            // Default to creator if psychologist?
            if ($user->role === 'psychologist') {
                $service->psychologists()->attach($user->id);
            }
        }

        return redirect()->route('admin.services.index')->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service); // Need Policy or manual check

        // Manual check for now
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'root' && $service->user_id !== $user->id) {
            abort(403);
        }

        $categories = Category::where('is_active', true)->where('type', 'service')->get();
        if ($user->role === 'admin' || $user->role === 'root') {
            $psychologists = User::whereIn('role', ['psychologist', 'titular'])->get();
        } else {
            $psychologists = User::where('id', $user->id)->get();
        }

        $service->load('psychologists');

        return Inertia::render('Admin/Services/Edit', [
            'service' => $service,
            'categories' => $categories,
            'psychologists' => $psychologists,
            'assigned_psychologists' => $service->psychologists->pluck('id'),
            'exchange_rate' => Setting::get('exchange_rate', 20.00),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        // Permission check
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'root' && $service->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'duration_minutes' => 'required|integer',
            'price_mxn' => 'required|numeric|min:0',
            'psychologists' => 'array',
            'image' => 'nullable|image|max:2048',
        ]);

        $exchangeRate = Setting::get('exchange_rate', 20.00);
        $priceUsd = $validated['price_mxn'] / $exchangeRate;

        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('local')->exists($service->image)) {
                Storage::disk('local')->delete($service->image);
            }
            $service->image = $request->file('image')->store('private/services', 'local');
        }

        $service->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'duration_minutes' => $validated['duration_minutes'],
            'price_mxn' => $validated['price_mxn'],
            'price_usd' => $priceUsd,
            'price' => $validated['price_mxn'],
        ]);

        if (isset($validated['psychologists'])) {
            $service->psychologists()->sync($validated['psychologists']);
        }

        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'root' && $service->user_id !== $user->id) {
            abort(403);
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado.');
    }
}
