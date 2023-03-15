<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'title',
        'views',
        'content',
        'created_by',
    ];

    protected $appends = ['created_at_format', 'update_at_format'];

    protected $attributes = [
        'views' => 0,
    ];

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function createdAtFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->translatedFormat('d F Y'),
        );
    }

    public function updateAtFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->translatedFormat('d F Y'),
        );
    }
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
    public function postsaves()
    {
        return $this->hasMany(PostSave::class);
    }
    public function tag()
    {
        return $this->belongsToMany(Tags::class, "post_tag", "post_id", "tag_id");
    }
}
