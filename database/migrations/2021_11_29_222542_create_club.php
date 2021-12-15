<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club', function (Blueprint $table) {
            $table->id();
            $table->string('reviewer_name', 50);
            $table->string('reviewer_image', 50);
            $table->integer('reviewer_rating');
            $table->string('reviewer_review', 255);
            $table->text('reviewer_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
//     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('club');
    // }
}
