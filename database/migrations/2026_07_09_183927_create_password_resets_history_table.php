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
        Schema::create('password_resets_history', function (Blueprint $table) {
            $table->id('ID');
            $table->string('EMAIL');
            $table->string('STATUS'); // 'SOLICITADO', 'COMPLETADO'
            $table->string('IP_ADDRESS')->nullable();
            $table->timestamp('CREATED_AT')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets_history');
    }
};
