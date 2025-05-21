<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orderan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan');
            $table->string('jenis_laundry');
            $table->string('berat');
            $table->string('harga');
            $table->string('pembayaran');
            $table->string('status');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderan');
    }
};
