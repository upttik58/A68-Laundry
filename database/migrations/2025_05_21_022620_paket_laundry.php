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
        Schema::create('paket_laundry', function (Blueprint $table) {
            $table->id();
            $table->string('kode_paket');
            $table->foreignId('jenis_laundry_id')->constrained('jenis_laundry')->onDelete('cascade');
            $table->string('berat');
            $table->string('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_laundry');
    }
};
