<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'role',
        'phone',
        'specialty',
        'gender',
        'profile_photo_path',
        'is_approved',
        'can_blog',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_blog' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the default profile photo URL if no photo has been uploaded.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? '/storage/' . $this->profile_photo_path
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function savedPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'saved_posts', 'user_id', 'blog_post_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function psychologistAppointments()
    {
        return $this->hasMany(Appointment::class, 'psychologist_id');
    }

    public function authorizedServices()
    {
        return $this->belongsToMany(Service::class, 'psychologist_service', 'psychologist_id', 'service_id');
    }

    public function createdServices()
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function myCreatedUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }
}
