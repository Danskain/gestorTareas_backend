<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fecha extends Model
{
    use HasFactory;
    
    protected $fillable = ['liga_id', 'fecha'];

    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }
}