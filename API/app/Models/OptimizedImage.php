<?php

namespace App\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptimizedImage extends Model
{
    use HasFactory;
    
    protected $fillable = ['image_id', 'version', 'path', 'url'];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
