<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, "post_categories", "categories_id ", "post_id");
    }
}
