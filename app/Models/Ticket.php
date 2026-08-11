<?php

namespace App\Models;

use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'category_id',
        'clues_at_report',        
        'department_at_report',   
        'title',
        'description',
        'status',
        'solucion',
        'attended_at',
        'resolved_at'
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value), 'UTF-8'),
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(trim($value), 'UTF-8'),
        );
    }

    protected function cluesAtReport(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    protected function departmentAtReport(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    protected function solucion(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    // Mutador para mantener el estado en MAYÚSCULAS
    protected function status(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? mb_strtoupper(trim($value), 'UTF-8') : null,
        );
    }

    // Relación: El empleado que creó el ticket
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: El técnico de soporte asignado
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relación: La categoría del problema
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación: La calificación del ticket
    public function rating()
    {
        return $this->hasOne(TicketRating::class);
    }

    public function messages() 
    { 
        return $this->hasMany(TicketMessage::class, 'ticket_id'); 

    }

    public function mensajes()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }


}