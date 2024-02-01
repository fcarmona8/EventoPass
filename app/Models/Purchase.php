<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'total_price', 'name', 'email', 'dni', 'phone'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
