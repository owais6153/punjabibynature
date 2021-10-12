<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('item', function (Blueprint $table) {
            $table->string('food_type', 255)->nullable();
            $table->unsignedBigInteger('catering_cat_id')->unsigned()->nullable();            
            $table->foreign('catering_cat_id')->references('id')->on('catering_category')->onDelete('set null');            
            $table->integer('cat_id')->unsigned()->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('food_type');
        $table->dropColumn('catering_cat_id');
    }
}
