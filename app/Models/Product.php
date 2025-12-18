<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price_mxn',
        'cover_path',
        'file_path',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_mxn' => 'decimal:2',
    ];

    protected $appends = [
        'cover_url',
        'price_usd',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCoverUrlAttribute()
    {
        return $this->cover_path
            ? Storage::url($this->cover_path)
            : 'https://placehold.co/400x300?text=No+Cover';
    }

    public function getPriceUsdAttribute()
    {
        $rate = Setting::get('exchange_rate', 20); // Default to 20 if not set
        return $rate > 0 ? round($this->price_mxn / $rate, 2) : 0;
    }
}
