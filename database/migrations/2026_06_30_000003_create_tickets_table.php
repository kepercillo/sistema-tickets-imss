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
        Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('category_id')->constrained('categories');
        $table->string('clues_at_report')->nullable();
        $table->string('department_at_report')->nullable();
        $table->string('title');
        $table->text('description');
        $table->string('status')->default('PENDIENTE');
        $table->text('solucion')->nullable();
        $table->timestamp('attended_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tickets');
        Schema::enableForeignKeyConstraints();
    }
};
