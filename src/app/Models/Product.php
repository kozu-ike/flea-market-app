<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'brand',
        'image',
        'condition',
        'stock',
        'user_id'
    ];

    public function isLikedByUser()
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function isSoldOut()
    {
        // return $this->stock === 0;
        return $this->is_sold;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
