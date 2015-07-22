<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users',function($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password',64);
            $table->string('name', 128);
            $table->enum('gender', array('Male', 'Female', 'Other'))->nullable();
            $table->string('phone_number', 16)->nullable();
            $table->date('date_of_birth')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }

}
