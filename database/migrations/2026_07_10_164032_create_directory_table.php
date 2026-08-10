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
        Schema::create('DIRECTORY', function (Blueprint $table) {
            $table->id('ID');
            $table->string('NOMBRE');
            $table->string('EMAIL')->nullable();
            $table->string('CUENTA')->nullable(); // Cuenta
            $table->string('PUESTO')->nullable(); // Puesto
            $table->string('COORDINACION')->nullable();
            $table->string('CURP', 18)->nullable(); // Sensible
            $table->string('RFC', 13)->nullable();  // Sensible
            $table->string('TELEFONO')->nullable();
            $table->string('OBSERVACIONES')->nullable(); // Sensible
            $table->timestamps(); // Control interno de Laravel (created_at, updated_at)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DIRECTORY');
    }
};
