<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'browser',
        'start_time',
        'end_time',
        'duration',
        'member_id'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    
    // In Activity.php model
public function member()
{
    return $this->belongsTo(Member::class, 'member_id','id');
}

}
