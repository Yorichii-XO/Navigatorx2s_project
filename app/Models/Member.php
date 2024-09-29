<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Specify the table if it's different from the default 'members'
    protected $table = 'members';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'invited_by',
        'user_id'
    ];

    /**
     * Relationship with the User who invited the member.
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
   // Define the relationship with Activity
   public function activities()
   {
       return $this->hasMany(Activity::class);
   }
}
