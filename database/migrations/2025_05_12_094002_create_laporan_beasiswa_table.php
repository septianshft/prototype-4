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
        Schema::create('laporan_beasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('acc_count')->default(0);
            $table->enum('status_acc', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('beasiswa_id')->nullable()->constrained('beasiswa')->onDelete('cascade');
            $table->string('nama_laporan');
            $table->string('file_path');
            $table->enum('jenis_laporan', ['progress', 'final'])->default('progress');
            $table->string('feedback')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_beasiswa');
    }
};
