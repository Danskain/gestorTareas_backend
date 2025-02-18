<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'liga_id',
        'user_id', // ID del usuario que creó el equipo
    ];

    /**
     * Obtener la liga a la que pertenece el equipo.
     */
    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    /**
     * Obtener el usuario que creó el equipo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener los partidos donde el equipo juega como local.
     */
    public function partidosComoLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local');
    }

    /**
     * Obtener los partidos donde el equipo juega como visitante.
     */
    public function partidosComoVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante');
    }

    /**
     * Obtener los partidos en los que el equipo descansa.
     */
    public function partidosDescanso()
    {
        return $this->hasMany(Partido::class, 'descansa');
    }
}
