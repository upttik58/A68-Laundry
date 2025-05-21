<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisLaundry extends Model
{
    protected $table = 'jenis_laundry';
    protected $guarded = ['id'];

    public function paketLaundry()
    {
        return $this->hasMany(PaketLaundry::class, 'jenis_laundry_id', 'id');
    }

    public function orderan()
    {
        return $this->hasMany(Orderan::class, 'jenis_laundry', 'id');
    }
}
