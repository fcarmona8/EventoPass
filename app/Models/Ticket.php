<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'type_id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class);
    }
}
