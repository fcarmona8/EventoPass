<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'total_price', 'name', 'email', 'dni', 'phone'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function generarCompra($session_id, $total_price, $name, $email, $dni, $phone){
        DB::beginTransaction();

    // Crea una nueva compra
    $purchase = Purchase::create([
        'session_id' => $session_id, 
        'total_price' => $total_price, 
        'name' =>  $name, 
        'email' => $email, 
        'dni' =>  $dni, 
        'phone' =>  $phone, 
    ]);

    DB::commit();
    }

}
