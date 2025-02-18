<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'user_id',
    ];

    /**
     * Obtener el usuario que creó la liga.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener los equipos de la liga.
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    /**
     * Obtener las fechas de la liga.
     */
    public function fechas()
    {
        return $this->hasMany(Fecha::class);
    }
}
