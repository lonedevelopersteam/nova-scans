<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, mixed $username)
 * @property mixed|string $role
 * @property mixed|string $password
 */
class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $fillable = ["username", "email", "password", "role", "device_id", "access_token"];
    protected $casts = [
        'access_token_expire' => 'datetime',
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function bookmarkSeries(): HasMany
    {
        return $this->hasMany(Users::class, 'user_id', 'id');
    }
}
