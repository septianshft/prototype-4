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
            $table->string('nama_mahasiswa');
            $table->string('nim'); // gunakan string untuk NIM agar bisa fleksibel
            $table->float('ipk', 3, 2); // 3 digit, 2 desimal, misalnya: 3.85   
            $table->string('email'); // sebaiknya ambil dari tabel user
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('role');
            $table->string('program_studi');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
