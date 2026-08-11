<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $appends = [
        'average_rating',
        'rating_count',
        'is_favorite',
        'display_image_url',
        'gallery_image_urls',
    ];

    protected $fillable = [
        'name',
        'description',
        'image',
        'images',
        'category',
        'status',
        'price',
        'views',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->comments()->whereNotNull('rating')->avg('rating') ?? 0, 1);
    }

    public function getRatingCountAttribute()
    {
        return $this->comments()->whereNotNull('rating')->count();
    }

    public function getIsFavoriteAttribute()
    {
        if (auth()->check()) {
            return $this->favorites()->where('user_id', auth()->id())->exists();
        }

        return in_array($this->id, session('favorite_items', []));
    }

    public function getDisplayImageUrlAttribute()
    {
        if ($this->image) {
            return $this->resolveImageUrl($this->image);
        }

        return 'https://picsum.photos/seed/item-' . $this->id . '/600/400';
    }

    public function getGalleryImageUrlsAttribute()
    {
        return collect($this->images ?? [])
            ->map(fn ($image) => $this->resolveImageUrl($image))
            ->values()
            ->all();
    }

    public function resolveImageUrl(string $image): string
    {
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, 'image/')) {
            return asset($image);
        }

        return asset('image/' . $image);
    }
}
