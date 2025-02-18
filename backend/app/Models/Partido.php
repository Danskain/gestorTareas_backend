<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_id',
        'equipo_local',
        'equipo_visitante',
        'marcador_local',
        'marcador_visitante',
        'descansa'
    ];

    public function fecha()
    {
        return $this->belongsTo(Fecha::class);
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante');
    }

    public function equipoDescansa()
    {
        return $this->belongsTo(Equipo::class, 'descansa');
    }
}