<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;


return new class extends Migration
{
    use HasFactory;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('ticket_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade'); // <-- AGREGAR CASCADE
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->text('message');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
        $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
