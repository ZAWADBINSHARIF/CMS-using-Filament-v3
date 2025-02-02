<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{

    protected $fillable = [
        'thumbnail',
        'title',
        'content',
        'color',
        'slug',
        'category_id',
        'tags',
        'published'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsToMany(User::class, 'user_post')->withPivot("order")->withTimestamps();
    }
}
