<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('attribute_product');
        Schema::create('attribute_product', function (Blueprint $table) {
            $table->bigIncrements('attribute_product_id');
            $table->bigInteger('attribute_id');
            $table->string('attribute_text')->nullable();
            $table->bigInteger('product_id');            
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
        Schema::dropIfExists('attribute_product');
    }
}
