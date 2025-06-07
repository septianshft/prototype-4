<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('apply_beasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('beasiswa_id')->constrained('beasiswa')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->string('file_persyaratan_path')->nullable();
            $table->string('feedback')->nullable();
            $table->enum('file_status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();
    });
    }

    public function down()
    {
        Schema::dropIfExists('apply_beasiswa');
    }
};
