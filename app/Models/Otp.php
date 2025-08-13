<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otp';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    public $incrementing = true;
    public $timestamps = true;
}
