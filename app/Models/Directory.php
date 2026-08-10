<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Directory extends Model
{
    protected $table = 'DIRECTORY';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'NOMBRE',
        'EMAIL',
        'CUENTA',
        'PUESTO',
        'COORDINACION',
        'CURP',
        'RFC',
        'TELEFONO',
        'OBSERVACIONES',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                // Si el campo es un texto y no son las fechas de control, lo pasa a MAYÚSCULAS
                if (is_string($value) && !in_array($key, ['created_at', 'updated_at'])) {
                    $model->attributes[$key] = Str::upper(trim($value));
                }
            }
        });
    }
}