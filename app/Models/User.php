<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

    public function getFilamentAvatarUrl(): ?string
    {
        //get avatar from UserProfile model where user_id is current user
        $avatar = \App\Models\UserProfile::where('user_id', auth()->user()->id)->value('avatar');
        //get the avatar from the storage
        if($avatar){
            $avatar = asset('storage/user_avatars/'.$avatar);
        }else{
            $avatar == null;
        }
        return $avatar;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Get the user profile associated with the user.
     */
    public function user_profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user emergency information associated with the user.
     */
    public function user_emergency_information(): HasOne
    {
        return $this->hasOne(EmergencyInformation::class);
    }

    /**
     * Get the user emergency information associated with the user.
     */
    public function user_emergency_contact(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    /**
     * Get all the riders profiles associated with the user.
     */
    public function rider_profile(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    /**
     * Get all the user_bookings associated with the user.
     */
    public function user_booking(): HasMany
    {
        return $this->hasMany(UserBooking::class);
    }

    /**
     * Get the user_account associated with the user.
     */
    public function user_account(): HasOne
    {
        return $this->hasOne(UserAccount::class);
    }

    /**
     * Get the credit_purchases associated with the user.
     */
    public function credit_purchases(): HasMany
    {
        return $this->hasMany(CreditPurchases::class);
    }

    /**
     * Get the bus_driver_info associated with the user.
     */
    public function bus_driver(): HasOne
    {
        return $this->hasOne(BusDriver::class);
    }

    /**
     * Determine whether the user can access the given panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {    
        $panelId = $panel->getID();
        $access = true;

        if($panelId === 'admin'){
            $access = $this->hasRole('admin_user') || $this->hasRole('driver_user') || $this->hasRole('super_admin');
        }else if($panelId === 'Busstop'){
            $access = $this->getRoleNames()->isNotEmpty();// && $this->hasVerifiedEmail();
        }

        return $access;
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            //get the last user id
            $last_user_id = User::latest()->first()->id;
            //get id from roles tabel where name is parent_user
            $parent_user_role_id = \Spatie\Permission\Models\Role::where('name', 'parent_user')->value('id');
            //set $last_user_id to 'model_id' and $parent_user_id to 'role_id' in model_has_roles table
            DB::table('model_has_roles')->insert([
                'role_id' => $parent_user_role_id,
                'model_type' => 'App\Models\User',
                'model_id' => $last_user_id,
            ]);

            //create a user account for the new user
            $user_account = new UserAccount();
            $user_account->user_id = $last_user_id;
            $user_account->save();

        });


    }
}


