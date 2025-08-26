<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'wp_users_role';
    protected $fillable = [
        'title',
        'slug',

    ];
    // public function users()
    // {
    //     return $this->hasMany(User::class, 'role', 'slug');
    // }

     const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date'; // If you don’t have this column, also set timestamps = false

    public $timestamps = true;
    protected $casts = [
        'created_date' => 'datetime',
        'updated_date' => 'datetime'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'wp_users_assigned_role',
            'role_id',
            'user_id'
        );
    }
}
