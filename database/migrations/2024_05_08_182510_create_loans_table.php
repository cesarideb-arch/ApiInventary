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
        Schema::create('loans', function (Blueprint $table)  {
                $table->id();
                $table->unsignedBigInteger('product_id'); // Assuming 'product_id' is the foreign key column name
                $table->foreign('product_id')->references('id')->on('products'); // Assuming 'products' is the table name for the referenced table
                $table->string('responsible', 30);
                $table->decimal('quantity', 6, 2);
                $table->date('date');
                $table->timestamps(); // Automatically creates 'created_at' and 'updated_at'
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
