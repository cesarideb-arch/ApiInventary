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
        $table->id();
        $table->string('name');
        $table->string('model')->nullable();
        $table->string('unit_measure')->nullable();
        $table->string('brand')->nullable();
        $table->integer('quantity');
        $table->text('description')->nullable();
        $table->decimal('price', 8, 2);
        $table->string('profile_image')->nullable();
        $table->string('provider')->nullable();
        $table->string('serie')->nullable();
        $table->string('observations', 50)->nullable();
        $table->string('category')->nullable();
        $table->string('location')->nullable();
        $table->timestamps(); // Esto creará automáticamente las columnas 'created_at' y 'updated_at'
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
