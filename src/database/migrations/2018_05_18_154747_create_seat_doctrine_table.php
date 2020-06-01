<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatDoctrineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_doctrine', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('seat_doctrine_fitting', function (Blueprint $table) {
            $table->unsignedInteger('doctrine_id');
            $table->unsignedInteger('fitting_id');
        });

        Schema::create('seat_doctrine_role', function (Blueprint $table) {
            $table->unsignedInteger('doctrine_id');
            $table->unsignedInteger('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_doctrine');
        Schema::dropIfExists('seat_doctrine_fitting');
        Schema::dropIfExists('seat_doctrine_role_id');
    }
}
