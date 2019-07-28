<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $fillable = [
        'amount', 'buyer_id', 'seller_id', 'transfer_group'
    ];
    public function buyer()
    {
        return $this->hasMany('App\User','id', 'buyer_id');
    }
    public function seller()
    {
        return $this->hasMany('App\User','id', 'seller_id');
    }
}