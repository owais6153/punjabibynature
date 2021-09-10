<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditItemTableAddComboColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->boolean('is_default_combo')->default(0);
            $table->string('combo_group_id', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
             $table->dropColumn('is_default_combo');
             $table->dropColumn('combo_group_id');
        });
    }
}
