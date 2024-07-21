<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Post extends Authenticatable
{
    protected $table = 'posts';

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'title',
        'thumbnail',
        'image',
        'content',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the relationship with the User model.
    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
