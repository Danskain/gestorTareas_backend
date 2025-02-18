<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFechasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fechas', function (Blueprint $table) {
            $table->id(); // ID de la fecha
            $table->unsignedBigInteger('liga_id'); // Relación con la liga
            $table->integer('fecha'); // Número de la fecha
            $table->timestamps(); // created_at y updated_at

            // Clave foránea que asocia la fecha a una liga
            $table->foreign('liga_id')->references('id')->on('ligas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fechas');
    }
}