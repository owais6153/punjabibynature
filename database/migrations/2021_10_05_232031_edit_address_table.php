<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('address', function (Blueprint $table) {
           $table->string('lat', 255)->nullable()->change();
           $table->string('lang', 255)->nullable()->change();
           $table->string('pincode', 255)->nullable()->change();
           $table->string('delivery_charge', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('address', function (Blueprint $table) {
          $table->string('lat', 255)->change();
           $table->string('lang', 255)->change();
           $table->string('pincode', 255)->change();
           $table->string('delivery_charge', 255)->change();
        });
    }
}
