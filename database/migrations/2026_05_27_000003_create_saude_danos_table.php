<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('saude_danos', function (Blueprint $table) {
            $table->id();
            $table->char('CID10', 8);
            $table->unsignedBigInteger('lista_id');
            $table->unsignedBigInteger('agente_id');
            $table->string('risco')->nullable();
            $table->timestamps();

            // Índices
            $table->index('CID10');
            $table->index('lista_id');
            $table->index('agente_id');

            // Chaves estrangeiras
            $table->foreign('lista_id')
                  ->references('id')->on('listas')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('agente_id')
                  ->references('id')->on('agentes')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saude_danos');
    }
};
