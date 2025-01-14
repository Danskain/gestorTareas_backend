<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = ['title', 'description'];

    /**
     * Relación muchos a muchos con el modelo User.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withPivot('status', 'updated_at')
            ->withTimestamps();
    }
}
