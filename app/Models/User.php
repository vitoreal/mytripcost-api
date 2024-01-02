<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telefone',
        'status_id',
        'google_id',
        'google_token',
        'google_refresh_token',
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
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function endereco()
    {
        return $this->hasOne(Endereco::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Check if user has admin role.
     *
     * @return mixed
     */
    public function isAdmin() {

        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == 'ADMIN')
            {
                return true;
            }
        }

        return false;
    }

    public function isRoot() {

        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == 'ROOT')
            {
                return true;
            }
        }

        return false;
    }

    public function isPadrao() {

        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == 'PADRAO')
            {
                return true;
            }
        }

        return false;
    }
}
