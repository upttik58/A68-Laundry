<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLocation extends Model
{
    protected $table = 'order_location';
    protected $guarded = ['id'];

    public function orderan()
    {
        return $this->belongsTo(Orderan::class, 'order_id');
    }
}
