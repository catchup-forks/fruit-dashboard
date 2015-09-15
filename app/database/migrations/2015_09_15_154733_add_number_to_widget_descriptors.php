<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumberToWidgetDescriptors extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_descriptors', function(Blueprint $table)
        {
            $table->integer('number')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widget_descriptors', function(Blueprint $table)
        {
            $table->dropColumn('number');
        });
    }

}
