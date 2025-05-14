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
        Schema::create('data_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Pindahkan ke atas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('nama_mahasiswa');
            $table->string('nim')->nullable(); // gunakan string untuk NIM agar bisa fleksibel
            $table->float('ipk', 3, 2)->nullable(); // 3 digit, 2 desimal, misalnya: 3.85   
            $table->string('program_studi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mahasiswa');
    }
};
