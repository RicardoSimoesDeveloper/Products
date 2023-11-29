<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at');
            $table->string('acao', 50);
            $table->string('login', 255);
            $table->string('tabela', 255);
            $table->string('chave', 255);
        });

        Schema::create('log_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id');
            $table->string('campo', 255);
            $table->string('valor_antigo', 255)->nullable()->default(null);
            $table->string('valor_novo', 255)->nullable()->default(null);

            $table->foreign('log_id')->references('id')->on('logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_details');
        Schema::dropIfExists('logs');
    }
};
