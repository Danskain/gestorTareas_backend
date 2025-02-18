<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id(); // ID del equipo
            $table->string('nombre'); // Nombre del equipo
            $table->unsignedBigInteger('liga_id'); // ID de la liga a la que pertenece el equipo
            $table->unsignedBigInteger('user_id'); // ID del usuario que creó el equipo
            $table->timestamps(); // created_at y updated_at

            // Clave foránea para la relación con la tabla `ligas`
            $table->foreign('liga_id')->references('id')->on('ligas')->onDelete('cascade');

            // Clave foránea para la relación con la tabla `users`
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 🔥 Restricción única para evitar que un usuario tenga más de un equipo en la misma liga
            $table->unique(['liga_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipos', function (Blueprint $table) {
            // Eliminar la restricción única si se revierte la migración
            $table->dropUnique(['liga_id', 'user_id']);
        });
    }
}
