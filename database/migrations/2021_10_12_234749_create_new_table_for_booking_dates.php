<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableForBookingDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_dates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('booking_id')->unsigned();            
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
            $table->string('status', 50)->default('booked');
            $table->dateTime('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_dates');
    }
}
