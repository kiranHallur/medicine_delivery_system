<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->enum('product_appearance', ['PHYSICAL', 'VIRTUAL'])->default('PHYSICAL');
            $table->bigInteger('manufacturer_id')->nullable();
            
            $table->float('length', 11, 2)->default(0);
            $table->float('width', 11, 2)->default(0);
            $table->float('height', 11, 2)->default(0);
            $table->enum('dimension_class', ['CM', 'MM', 'INCH'])->default('CM');
            
            $table->float('weight', 11, 2)->default(0);
            $table->enum('weight_class', ['KG', 'GRAM'])->default('GRAM');
            
            $table->tinyInteger('status')->default(1);
            $table->string('image')->nullable();
            $table->bigInteger('sort_order')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
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
}
