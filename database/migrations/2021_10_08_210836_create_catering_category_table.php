<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_category', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('option_allowed')->nullable();
            $table->integer('allowed_veg')->nullable();
            $table->integer('allowed_nonveg')->nullable();
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
        Schema::dropIfExists('catering_category');
    }
}
