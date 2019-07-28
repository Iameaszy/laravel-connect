<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $_fillable = [
        'name', 'email', 'password',
    ];
    protected $fillable = [
        'name', 'email', 'password', 'category','is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $_hidden = [
        'password', 'remember_token','is_admin'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $_casts = [
        'email_verified_at' => 'datetime',
    ];
}
