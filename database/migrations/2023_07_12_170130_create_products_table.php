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
        Schema::create('products', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('type');
			$table->unsignedBigInteger('brand');
            $table->string('description', 50)->nullable();
			$table->decimal('price', 7,2);
            $table->integer('stock')->nullable();

            $table->timestamp('created_at');
			$table->timestamp('updated_at');
            $table->softDeletes('deleted_at');

            $table->foreign('brand')->references('id')->on('brands');
            $table->foreign('type')->references('id')->on('types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
