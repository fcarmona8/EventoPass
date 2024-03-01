<?php

namespace App\Models;

use App\Models\OptimizedImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function optimizedImages()
    {
        return $this->hasMany(OptimizedImage::class);
    }
}
