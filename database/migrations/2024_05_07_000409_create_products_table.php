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
        $table->string('measurement_unit')->nullable();
        $table->string('brand')->nullable();
        $table->integer('quantity');
        $table->text('description')->nullable();
        $table->decimal('price', 8, 2);
        $table->string('profile_image')->nullable();
        $table->string('serie')->nullable();
        $table->string('observations', 50)->nullable();
        $table->string('location')->nullable();
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('supplier_id');
        $table->foreign('category_id')->references('id')->on('categories');
        $table->foreign('supplier_id')->references('id')->on('suppliers');
        $table->timestamps(); // Esto creará automáticamente las columnas 'created_at' y 'updated_at'
    });
}

// public function up()
// {
//     Schema::create('products', function (Blueprint $table) {
//         $table->id();
//         $table->string('name', 100);
//         $table->string('model', 100)->nullable();
//         $table->string('measurement_unit', 10)->nullable();
//         $table->string('brand', 100)->nullable();
//         $table->decimal('price');
//         $table->integer('quantity');
//         $table->text('description')->nullable();
//         $table->string('product_image', 100)->nullable();
//         $table->string('serie', 100)->nullable();
//         $table->text('observations')->nullable();
//         $table->string('location', 100)->nullable();
//         $table->unsignedBigInteger('category_id');
//         $table->unsignedBigInteger('supplier_id');
//         $table->foreign('category_id')->references('id')->on('categories');
//         $table->foreign('supplier_id')->references('id')->on('suppliers');
//         $table->timestamps();
//     });
// }


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
