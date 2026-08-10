<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoDepartament extends Model
{
    //
    protected $table = 'catalogo_departaments';
    protected $fillable = ['name'];


    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value), 'UTF-8'),
        );
    }
}
