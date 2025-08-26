<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'wp_users';
    protected $fillable = [
        'user_login',
        'user_pass',
        'user_nicename', //concat of first_name and last_name without space and small letters
        'user_email',
        'user_url',
        'user_registered', //timestamp
        'user_activation_key',
        'resend_key_date',
        'resend_pin_type',
        'user_status',
        'display_name', //concat of first_name and last_name with space
        'facebook_id',
        'google_id',
        'facebook_email',
        'google_email',
        'business_name',
        'business_email',
        'profile_picture',
        'business_cover',
        'gender',
        'birthday',
        'country',
        'phone_number',
        'address',
        'preferences',
        'cash_limit_payment',
        'cash_payment_banned',
        'accept_cash',
        'new_partners',
        'partner_slug',
        'is_verified',
        'activation_link',
        'email_activation_code',
        'password_reset_code',// for password reset
        'access_token',
        'access_token_created_at',
        'temp_token',
        'agency_pin',
        'agency_id',
        'agency_permissions',
        'ref_cart',
        'ref_cart_expiration',
        'ref_link_code',
        'with_transaction',
      
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_pass',
     
        'remember_token',
    ];

    const CREATED_AT = 'user_registered';
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
 public function roles()
    {
        return $this->belongsToMany(
            UserRole::class,
            'wp_users_assigned_role',
            'user_id',
            'role_id'
        )->distinct();  
    }
}
