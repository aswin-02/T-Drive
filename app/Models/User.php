<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'mobile',
        'user_type',
        'image',
        'dob',
        'gender',
        'address',
        'state_id',
        'district_id',
        'city_id',
        'pincode_id',
        'pan_card',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'bank_details_approved',
        'bank_details_rejection_reason',
        'role_id',
        'password',
        'password1',
        'is_active',
        'invite_code',
        'referred_by',
    ];
    /**
     * The user who referred this user.
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'user_id');
    }

    /**
     * Users referred by this user.
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * User's state
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * User's district
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * User's city
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * User's pincode
     */
    public function pincode()
    {
        return $this->belongsTo(Pincode::class, 'pincode_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_photo',
    ];
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
    public function getRoleNameAttribute()
    {
        return $this->roles->pluck('name')->first();
    }
    public function getProfilePhotoAttribute()
    {
        if ($this->image) {
            return generate_file_url($this->image);
        }

        // Return gender-based default image
        return $this->gender == 'Female'
            ? asset('user/assets/img/female.png')
            : asset('user/assets/img/male.png');
    }

    /**
     * Get the current wallet balance from transactions.
     *
     * @return float
     */

    /**
     * Calculate wallet balance as total credits minus total debits from transactions.
     *
     * @return float
     */
    public function getWalletBalanceAttribute(): float
    {
        $credits = $this->hasMany(Transaction::class, 'user_id')->where('type', 'credit')->sum('amount');
        $debits = $this->hasMany(Transaction::class, 'user_id')->where('type', 'debit')->sum('amount');
        return (float) ($credits - $debits);
    }

    /**
     * Get all transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get all withdrawal requests for the user.
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'user_id');
    }

    /**
     * Get the user's recent file views.
     */
    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }

}
