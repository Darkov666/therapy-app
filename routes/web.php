<?php

use App\Models\Service;
use App\Models\BlogPost;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'featuredServices' => Service::where('is_active', true)->take(3)->get(),
        'latestPosts' => BlogPost::where('is_published', true)->latest('published_at')->take(3)->get(),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/shop', function () {
    return Inertia::render('Shop', [
        'products' => \App\Models\Product::where('is_active', true)
            ->with(['category', 'user']) // Eager load category and seller
            ->latest()
            ->get(),
        'categories' => \App\Models\Category::where('type', 'product')
            ->where('is_active', true)
            ->get()
    ]);
});

Route::get('/services', function () {
    return Inertia::render('Services', [
        'services' => Service::where('is_active', true)
            ->whereIn('type', ['individual', 'couple', 'family', 'workshop', 'conference', 'talk', 'training'])
            ->with('category')
            ->get(),
        'categories' => \App\Models\Category::where('type', 'service')
            ->where('is_active', true)
            ->get()
    ]);
});

Route::get('/special-services', function () {
    return Inertia::render('SpecialServices', [
        'services' => Service::where('is_active', true)->where('type', 'special')->get()
    ]);
})->name('special-services');

// Booking Routes
Route::get('/booking/individual', [BookingController::class, 'showIndividualForm'])->name('booking.individual');
Route::post('/booking/individual', [BookingController::class, 'storeIndividual'])->name('booking.store.individual');
Route::get('/booking/group', [BookingController::class, 'showGroupForm'])->name('booking.group');
Route::post('/booking/group', [BookingController::class, 'storeGroup'])->name('booking.store.group');
Route::get('/booking/special', [BookingController::class, 'showSpecialForm'])->name('booking.special');
Route::post('/booking/special', [BookingController::class, 'storeSpecial'])->name('booking.store.special');
Route::get('/booking/workshop', [BookingController::class, 'showWorkshopForm'])->name('booking.workshop');
Route::post('/booking/workshop', [BookingController::class, 'storeWorkshop'])->name('booking.store.workshop');
Route::get('/booking/{appointment}/date', [BookingController::class, 'selectDate'])->name('booking.date');
Route::post('/booking/{appointment}/date', [BookingController::class, 'saveDate'])->name('booking.save-date');
Route::get('/booking/{appointment}/payment', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/{appointment}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
Route::get('/booking/{token}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
Route::get('/booking/{token}/reject', [BookingController::class, 'reject'])->name('booking.reject');
Route::post('/booking/{appointment}/accept', [BookingController::class, 'acceptReservation'])->name('booking.accept'); // Admin accept
Route::get('/booking/{appointment}/accept-signed', [BookingController::class, 'acceptReservationSigned'])->name('booking.accept.signed')->middleware('signed');

Route::get('/downloads/{token}', [App\Http\Controllers\DownloadsController::class, 'index'])->name('downloads.index');

Route::middleware([
    'auth',
    'verified',
    'approved',
])->group(function () {
    Route::get('/approval-pending', function () {
        return Inertia::render('Auth/ApprovalNotice');
    })->name('approval.notice')->withoutMiddleware(['approved']);

    // Client Dashboard (Default)
    // Client Dashboard (Default)
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'titular' || $user->role === 'psychologist') {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Dashboard', [
            'appointments' => $user->appointments()->with('service')->latest()->get()
        ]);
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::delete('/profile/sessions', [App\Http\Controllers\ProfileController::class, 'destroySessions'])->name('profile.sessions.destroy');

    // Admin Routes
    // Admin Routes
    Route::middleware(['role:admin|titular|psychologist'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

        // Settings (Admin Only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/settings', [App\Http\Controllers\Admin\AdminController::class, 'settings'])->name('settings');
            Route::put('/settings', [App\Http\Controllers\Admin\AdminController::class, 'updateSettings'])->name('settings.update');
            Route::put('users/{user}/approve', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.approve');
        });

        // User Management
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        // Service Management
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'edit', 'show']);

        // Blog Management
        Route::post('blog/upload-image', [App\Http\Controllers\Admin\BlogController::class, 'uploadEditorImage'])->name('blog.upload-image');
        Route::resource('blog', App\Http\Controllers\Admin\BlogController::class);
    });

    // Titular Routes
    Route::middleware(['auth', 'verified', 'role:titular|psychologist'])->prefix('titular')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('titular.dashboard');
    });

    // Secure Media Route
    Route::get('/media/secure', [App\Http\Controllers\MediaController::class, 'serve'])
        ->middleware('signed')
        ->name('media.secure');
});

// Auth routes are handled by Jetstream/Fortify

// Workshop Routes
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops.index');
Route::post('/workshops/meeting', [App\Http\Controllers\WorkshopController::class, 'requestMeeting'])->name('workshops.meeting');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/comment', [BlogController::class, 'comment'])->name('blog.comment');
Route::post('/blog/{slug}/like', [BlogController::class, 'like'])->name('blog.like');
Route::post('/blog/{slug}/save', [BlogController::class, 'save'])->name('blog.save')->middleware('auth');
Route::post('/comments/{comment}/like', [BlogController::class, 'likeComment'])->name('comments.like');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');

// Scheduling Routes
Route::get('/scheduling', [App\Http\Controllers\SchedulingController::class, 'index'])->name('scheduling.index');
Route::post('/scheduling', [App\Http\Controllers\SchedulingController::class, 'store'])->name('scheduling.store');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Contact Routes
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::middleware(['auth', 'verified'])->post('/contact/{message}/reply', [App\Http\Controllers\ContactController::class, 'reply'])->name('contact.reply');

// User Messages Route (Profile)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile/messages', function () {
        return Inertia::render('Profile/Messages', [
            'messages' => App\Models\ContactMessage::where('user_id', Auth::id())
                ->whereNull('parent_id')
                ->with('replies')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    })->name('profile.messages');
});

// Admin Messages Routes
Route::middleware(['auth', 'verified', 'role:admin|titular|psychologist'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class)->only(['index', 'show']);
    Route::post('messages/{message}/reply', [App\Http\Controllers\Admin\MessageController::class, 'store'])->name('messages.reply');
});

Route::get('/debug-data', function () {
    return ['services' => \App\Models\Service::all()];
});
