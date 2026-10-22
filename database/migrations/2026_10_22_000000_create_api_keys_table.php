<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            // Solo se guarda el hash SHA-256: la clave en texto plano se muestra una única vez al crearla.
            $table->string('key_hash', 64)->unique();
            $table->timestamp('ultimo_uso_at')->nullable();
            $table->timestamp('revocada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
