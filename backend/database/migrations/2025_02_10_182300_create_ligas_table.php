<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLigasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ligas', function (Blueprint $table) {
            $table->id(); // ID de la liga
            $table->string('nombre'); // Nombre de la liga
            $table->text('descripcion')->nullable(); // Descripción de la liga (opcional)
            $table->unsignedBigInteger('user_id'); // ID del usuario que creó la liga
            $table->timestamps(); // created_at y updated_at

            // Clave foránea para la relación con la tabla `users`
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ligas');
    }
}
