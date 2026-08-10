<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_read',
        ];

        // Relación con el Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    // Relación con el Usuario que envía el mensaje
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
