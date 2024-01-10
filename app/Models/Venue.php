<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{

    use HasFactory;
    
    protected $fillable = ['name', 'location', 'capacity'];

    public function events() { return $this->hasMany(Event::class); }
}

