<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWidgetTypes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $typeChoices = "";
        foreach (Config::get('constants.WIDGET_TYPES') as $widgetType) {
            $typeChoices .= "'$widgetType',";
        }
        $typeChoices = rtrim($typeChoices, ",");
        DB::statement("ALTER TABLE widget_descriptors CHANGE COLUMN type type ENUM($typeChoices)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->up();
    }

}
