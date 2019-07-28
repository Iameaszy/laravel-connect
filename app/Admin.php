<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';
    protected $fillable = ['email','password'];
    protected $_hidden = [
        'password', 'remember_token',
    ];
    public function getAuthPassword()
    {
     return $this->password;
    }
}