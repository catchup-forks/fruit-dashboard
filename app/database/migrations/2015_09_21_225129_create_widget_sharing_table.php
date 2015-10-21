<?php
namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetSharingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('widget_sharings', function ($table) {
            $table->increments('id');

            $table->integer('src_user_id')->unsigned();
            $table->foreign('src_user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->integer('widget_id')->unsigned();
            $table->foreign('widget_id')
                  ->references('id')->on('widgets')
                  ->onDelete('cascade');

            $table->enum('state', array('not_seen', 'seen', 'accepted', 'rejected'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('widget_sharings');
    }
}
