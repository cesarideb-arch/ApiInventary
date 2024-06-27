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
            Schema::create('loans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('user_id');
                $table->string('responsible', 100);
                $table->integer('quantity');
                // $table->string('date', 100);
                $table->integer('status');
                $table->string('observations', 50)->nullable();
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('product_id')->references('id')->on('products');
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
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
