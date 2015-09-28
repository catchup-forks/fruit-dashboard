<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->enum('type', array('email', 'slack'));
            $table->enum('frequency', array('hourly', 'daily', 'weekly', 'monthly', 'yearly'));
            $table->string('address', 255)->nullable();

            $table->tinyInteger('send_minute')->nullable();
            $table->time('send_time')->nullable();
            $table->enum('send_weekday', array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'))->nullable();
            $table->tinyInteger('send_day')->nullable();
            $table->enum('send_month', array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'))->nullable();
        });

        foreach (User::all() as $user) {
            /* Create default email notification */
            $emailNotification = new Notification(array(
                'type' => 'email',
                'frequency' => 'daily',
                'address' => $user->email,
                'send_time' => Carbon::createFromTime(7, 0, 0, 'Europe/Budapest')
            ));
            $emailNotification->user()->associate($user);
            $emailNotification->save();

            /* Create default slack notification */
            $slackNotification = new Notification(array(
                'type' => 'slack',
                'frequency' => 'daily',
                'address' => null,
                'send_time' => Carbon::createFromTime(7, 0, 0, 'Europe/Budapest')
            ));
            $slackNotification->user()->associate($user);
            $slackNotification->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }

}