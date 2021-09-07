<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IngredientTypeIdColumnInIngredient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ingredients', function (Blueprint $table) {
             $table->integer('type_id')->unsigned()->nullable();
             $table->foreign('type_id')->references('id')->on('ingredient_types')
             ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ingredient', function (Blueprint $table) {
             $table->dropColumn('type_id');
        });
    }
}
