<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attribute;

class Category extends Model
{
    //
    protected $filable = ['name'];

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper ($value, 'UTF-8'),
        );
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
