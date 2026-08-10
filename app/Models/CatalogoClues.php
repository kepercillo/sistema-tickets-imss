<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class CatalogoClues extends Model
{
    //
    protected $table = 'catalogo_clues';
    protected $primaryKey ='clues';
    public $incrementing =false;
    protected $keyType = 'string';

    protected $fillable = [
        'clues', 'nombre_unidad', 'nombre_comercial', 'region', 'nombre_institucion',
        'clave_entidad', 'entidad', 'municipio', 'localidad', 'clave_jurisdiccion',
        'jurisdiccion', 'nombre_tipo_establecimiento', 'clave_tipo_vialidad',
        'tipo_vialidad', 'vialidad', 'numero_exterior', 'numero_interior',
        'clave_tipo_asentamiento', 'tipo_asentamiento', 'asentamiento', 'codigo_postal',
        'referencias_domicilio', 'telefono', 'nivel_atencion', 'clave_estrato_unidad',
        'estrato_unidad', 'clave_tipo_obra', 'tipo_obra', 'clave_propiedad_inmueble',
        'propiedad_inmueble', 'observaciones', 'latitud', 'longitud'
    ];
    
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value)=> mb_strtoupper(trim($value), 'UTF-8'),
        );
    }

    protected static function booted()
    {
        static::saving(function ($cluesModel) {
            foreach ($cluesModel->getAttributes() as $key => $value) {
                // Evitamos transformar los campos de fecha de Laravel y valores nulos
                if (!in_array($key, ['created_at', 'updated_at']) && is_string($value)) {
                    $cluesModel->attributes[$key] = mb_strtoupper(trim($value), 'UTF-8');
                }
            }
        });
    }
  
}
