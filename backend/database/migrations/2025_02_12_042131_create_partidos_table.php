<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id(); // ID del partido
            $table->unsignedBigInteger('fecha_id'); // Relación con la fecha del partido
            $table->unsignedBigInteger('equipo_local')->nullable(); // ID del equipo local
            $table->unsignedBigInteger('equipo_visitante')->nullable(); // ID del equipo visitante
            $table->integer('marcador_local')->nullable(); // Goles del equipo local
            $table->integer('marcador_visitante')->nullable(); // Goles del equipo visitante
            $table->unsignedBigInteger('descansa')->nullable(); // ID del equipo que descansa
            $table->timestamps(); // created_at y updated_at

            
            // Claves foráneas para la relación con la tabla `equipos`
            $table->foreign('equipo_local')->references('id')->on('equipos')->onDelete('set null');
            $table->foreign('equipo_visitante')->references('id')->on('equipos')->onDelete('set null');
            $table->foreign('descansa')->references('id')->on('equipos')->onDelete('set null');
            
            // Clave foránea para la relación con la tabla `fechas`
            $table->foreign('fecha_id')->references('id')->on('fechas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('partidos');
    }
};
