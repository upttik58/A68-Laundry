<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketMember extends Model
{
    protected $table = 'paket_member';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function paketLaundry(){
        return $this->belongsTo(PaketLaundry::class, 'paket_laundry_id', 'id');
    }
}
