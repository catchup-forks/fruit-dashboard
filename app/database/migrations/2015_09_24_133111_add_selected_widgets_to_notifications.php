<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelectedWidgetsToNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function(Blueprint $table)
        {
            $table->longText('selected_widgets')->nullable();
        });

        foreach (User::all() as $user) {
            /* Get all selectable widget */
            $selectedWidgets = array();           
            foreach ($user->widgets as $widget) {
                if ($widget->canSendInNotification()) {
                    array_push($selectedWidgets, $widget->id);
                }
            };

            /* Add to notification as selected */
            foreach ($user->notifications as $notification) {
                $notification->selected_widgets = json_encode($selectedWidgets);
                $notification->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function(Blueprint $table)
        {
             $table->dropColumn('selected_widgets');
        });
    }

}
