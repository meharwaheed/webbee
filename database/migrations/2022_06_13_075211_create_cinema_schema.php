<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different showrooms

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('cinemas', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('location');
            $table->timestamps();
        });

        Schema::create('movies', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image_name');
            $table->date('release_date');
            $table->timestamps();
        });

        Schema::create('show_rooms', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('show_time');
            $table->integer('cinema_id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->foreign('cinema_id')->references('id')->on('cinemas')->onDelete('cascade');
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->timestamps();
        });

//        we can use this to add seat types
//        * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip s
         Schema::create('seat_types', function($table) {
            $table->increments('id');
            $table->string('name'); // to store line name like A, B, C
            $table->string('type'); // to store type like VIP, Basic, Premium etc.
            $table->double('price');
            $table->timestamps();
        });

        Schema::create('showroom_seating_structures', function($table) {
            $table->increments('id');
            $table->integer('showroom_id')->unsigned();
            $table->foreign('showroom_id')->references('id')->on('show_rooms')->onDelete('cascade');

            $table->integer('seat_type_id')->unsigned();
            $table->foreign('seat_type_id')->references('id')->on('seat_types')->onDelete('cascade');

            $table->integer('no_of_seats')->unsigned()->default(1);

            $table->timestamps();
        });

        Schema::create('showroom_tickets', function($table) {
            $table->increments('id');
            $table->string('name'); // to store line name like 1, 2,3 ... etc
            $table->integer('showroom_seating_structure_id')->unsigned();
            $table->foreign('showroom_seating_structure_id')->references('id')->on('showroom_seating_structures')->onDelete('cascade');
            $table->tinyInteger('is_booked')->default(0);
            $table->integer('booked_by_user')->unsigned();
            $table->foreign('booked_by_user')->references('id')->on('users')->onDelete('cascade');
            $table->double('price');
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
        Schema::dropIfExists('cinemas');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('show_rooms');
        Schema::dropIfExists('seat_types');
        Schema::dropIfExists('showroom_seating_structures');
        Schema::dropIfExists('showroom_tickets');
    }
}
