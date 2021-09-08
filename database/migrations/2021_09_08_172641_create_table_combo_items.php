<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableComboItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combo_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('combo_group')->onDelete('set null');
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
        Schema::dropIfExists('combo_items');
    }
}

