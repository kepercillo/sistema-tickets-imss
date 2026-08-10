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
        Schema::create('catalogo_clues', function (Blueprint $table) {
            $table->string('clues')->primary(); // CLUES
            $table->string('nombre_unidad')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('region')->nullable();
            $table->string('nombre_institucion')->nullable();
            $table->string('clave_entidad')->nullable();
            $table->string('entidad')->nullable();
            $table->string('municipio')->nullable();
            $table->string('localidad')->nullable();
            $table->string('clave_jurisdiccion')->nullable();
            $table->string('jurisdiccion')->nullable();
            $table->string('nombre_tipo_establecimiento')->nullable();
            $table->string('clave_tipo_vialidad')->nullable();
            $table->string('tipo_vialidad')->nullable();
            $table->string('vialidad')->nullable();
            $table->string('numero_exterior')->nullable();
            $table->string('numero_interior')->nullable();
            $table->string('clave_tipo_asentamiento')->nullable();
            $table->string('tipo_asentamiento')->nullable();
            $table->string('asentamiento')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->text('referencias_domicilio')->nullable();
            $table->string('telefono')->nullable();
            $table->string('nivel_atencion')->nullable();
            $table->string('clave_estrato_unidad')->nullable();
            $table->string('estrato_unidad')->nullable();
            $table->string('clave_tipo_obra')->nullable();
            $table->string('tipo_obra')->nullable();
            $table->string('clave_propiedad_inmueble')->nullable();
            $table->string('propiedad_inmueble')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
        $table->foreign('clues')
              ->references('clues')
              ->on('catalogo_clues')
              ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['clues']);
    });
    
        Schema::dropIfExists('catalogo_clues');
    }
};
