<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
           $table->string('ingredients', 255)->nullable();
           $table->string('combo', 255)->nullable();
           $table->string('group_addons', 255)->nullable();
           $table->integer('totalAddonPrice')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
             $table->dropColumn('ingredients');
             $table->dropColumn('combo');
             $table->dropColumn('group_addons');
             $table->dropColumn('totalAddonPrice');

        });
    }
}
