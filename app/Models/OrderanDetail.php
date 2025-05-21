<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderanDetail extends Model
{
    protected $table = 'detail_orderan_offline';
    protected $guarded = ['id'];

    public function orderan()
    {
        return $this->belongsTo(Orderan::class, 'orderan_id');
    }
}
