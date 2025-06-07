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
        Schema::create('beasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->String('nama_beasiswa');
            $table->string('nama_penyelenggara');
            $table->string('periode');
            $table->integer('kuota')->default(1);
            $table->enum('status', ['open', 'full'])->default('open');
            $table->foreignId('program_studi_id')->constrained('program_studi');
            $table->text('deskripsi')->nullable();
            $table->boolean('require_file')->default(false);
            $table->string('persyaratan_file_name')->nullable();
            $table->date('deadline_pendaftaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beasiswa');
    }
};
