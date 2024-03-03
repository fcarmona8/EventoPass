<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'available_tickets'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'type_id');
    }
}
