<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('data',function($table) {
            /* Adding dataManager fields. */
            $table->integer('user_id')->unsigned();
            $table->integer('descriptor_id')->unsigned();
            $table->integer('update_period')->default(60);
            $table->string('criteria', 255)->nullable();
            $table->enum('state', array('active', 'loading'))->default('active');
        });
        Log::info("Added DM fields to data table");
        foreach (DB::table('data_managers')->get() as $dm) {
            /* Copying data */
            /* Checking integrity of manager. */
            if (is_null(User::find($dm->user_id))) { continue; }
            if (is_null(WidgetDescriptor::find($dm->descriptor_id))) { continue; }
            $data = Data::find($dm->data_id);
            $data->user_id = $dm->user_id;
            $data->descriptor_id = $dm->descriptor_id;
            $data->criteria = $dm->settings_criteria;
            $data->update_period = $dm->update_period;
            $data->state = $dm->state;
            $data->save();
            Log::info('Copied values from dm #' . $dm->id . ' to data #'. $data->id);
        }
        Log::info('Adding foreign constraints.');
        DB::table('data')->where('user_id', 0)->delete();
        Schema::table('data',function($table) {
            /* Adding cascaded foreigns. */
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('descriptor_id')
                  ->references('id')->on('widget_descriptors')
                  ->onDelete('cascade');
        });
        Log::info('Dropping data_managers table.');
        Schema::dropIfExists('data_managers');
        Log::info('Migration finished.');
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }

}
