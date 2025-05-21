<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderan extends Model
{
    protected $table = 'orderan';
    protected $guarded = ['id'];

    public function orderanDetail()
    {
        return $this->hasOne(OrderanDetail::class, 'orderan_id');
    }

    public function jenisLaundry()
    {
        return $this->belongsTo(JenisLaundry::class, 'jenis_laundry');
    }
}
