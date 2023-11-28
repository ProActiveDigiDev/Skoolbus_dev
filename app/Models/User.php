<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements FilamentUser, HasAvatar
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
     * Get all the riders profiles associated with the user.
     */
    public function rider_profile(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {    
        $panelId = $panel->getID();
        $access = true;

        // if($panelId === 'admin'){
        //     $access = $this->hasRole('admin') || $this->hasRole('super_admin');
        // }else if($panelId === 'Busstop'){
        //     $access = $this->getRoleNames()->isNotEmpty(); // && $this->hasVerifiedEmail();
        //     $access = true;
        // }

        return $access;
    }
}
