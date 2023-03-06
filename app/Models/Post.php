<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
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

    protected $attributes = [
        'views' => 0,
    ];
    public function comment()
    {
        return $this->hasMany(Comment::class);
    }
}
