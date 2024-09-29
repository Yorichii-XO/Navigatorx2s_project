<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'visit_time',
        'duration',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Optionally, define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
