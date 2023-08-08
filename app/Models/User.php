<?php

namespace App\Models;

use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Search;

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
    ];

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<int, Role>
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the admin user associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<MatchCriterion>
     */
    public function matchCriterion()
    {
        return $this->hasMany(MatchCriterion::class);
    }

    public function getLoggedInUser() : User
    {
        $user = auth()->user();
        if ($user) {
            // get first user with admin role
            /** @var User $user */
            $user = User::query()->firstWhere('id', $user->getAuthIdentifier());
        }
        if (!$user) {
            // get first user with admin role
            $user = User::query()->firstWhere('role_id', 1);
        }

        return $user;
    }
}
