<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketLaundry extends Model
{
    protected $table = 'paket_laundry';
    protected $guarded = ['id'];

    public function jenisLaundry()
    {
        return $this->belongsTo(JenisLaundry::class, 'jenis_laundry_id', 'id');
    }

    public function paketMember()
    {
        return $this->hasMany(PaketMember::class, 'paket_laundry_id', 'id');
    }
}
