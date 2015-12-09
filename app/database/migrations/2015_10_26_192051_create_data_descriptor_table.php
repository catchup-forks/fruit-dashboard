<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataDescriptorTable extends Migration
{
    private static $matchingTable = array(
        /* Braintree */
        'braintree_arpu' => array(
            'category' => 'braintree',
            'type'     => 'arpu'
        ),
        'braintree_arr' => array(
            'category' => 'braintree',
            'type'     => 'arr'
        ),
        'braintree_mrr' => array(
            'category' => 'braintree',
            'type'     => 'mrr'
        ),

        /* Facebook */
        'facebook_engaged_users' => array(
            'category' => 'facebook',
            'type'     => 'engaged_users',
            'reinit'   => true
        ),
        'facebook_likes' => array(
            'category' => 'facebook',
            'type'     => 'likes',
            'reinit'   => true
        ),
        'facebook_page_impressions' => array(
            'category' => 'facebook',
            'type'     => 'page_impressions'
        ),

        /* Twitter */
        'twitter_followers' => array(
            'category' => 'twitter',
            'type'     => 'followers'
        ),
        'twitter_mentions' => array(
            'category' => 'twitter',
            'type'     => 'mentions'
        ),

        /* Google analytics */
        'google_analytics_active_users' => array(
            'category' => 'google_analytics',
            'type'     => 'active_users',
        ),
        'google_analytics_avg_session_duration' => array(
            'category' => 'google_analytics',
            'type'     => 'avg_session_duration'
        ),
        'google_analytics_bounce_rate' => array(
            'category' => 'google_analytics',
            'type'     => 'bounce_rate'
        ),
        'google_analytics_goal_completion' => array(
            'category' => 'google_analytics',
            'type'     => 'goal_completion',
            'reinit'   => true
        ),
        'google_analytics_sessions' => array(
            'category' => 'google_analytics',
            'type'     => 'sessions',
            'reinit'   => true
        ),
        'google_analytics_users' => array(
            'category' => 'google_analytics',
            'type'     => 'new_users',
            'reinit'   => true
        ),

        /* Personal */
        'quote' => array(
            'category' => 'personal',
            'type'     => 'quote'
        ),

        /* Stripe */
        'stripe_arpu' => array(
            'category' => 'stripe',
            'type'     => 'arpu'
        ),
        'stripe_arr' => array(
            'category' => 'stripe',
            'type'     => 'arr'
        ),
        'stripe_mrr' => array(
            'category' => 'stripe',
            'type'     => 'mrr'
        ),

        /* Webhook/API */
        'api_histogram' => array(
            'category' => 'webhook_api',
            'type'     => 'api'
        ),
        'webhook_histogram' => array(
            'category' => 'webhook_api',
            'type'     => 'webhook'
        ),
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Creating table. */
        $this->removeWidgetDataRelation();

        /* Creating table. */
        $this->createTable();
    
        /* Seeding data descriptors. */
        $this->seed();

        /* Adding contraint. */
        $this->removeConstraint();

        /* Reassigning foreigns. */
        $this->assignDescriptors();
    
        /* Adding contraint. */
        $this->addConstraint();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_descriptors');
    }

    /**
     * Creating the table attributes.
     *
     * @return void
     */
    private function createTable()
    {
        Schema::create('data_descriptors', function($table) {
            $table->increments('id');
            $table->string('category', 127);
            $table->string('type', 127);
            $table->string('attributes', 255)->nullable();
        });
    }

    /**
     * Calling seeder.
     *
     * @return void
     */
    private function seed()
    {
        $seeder = new DataDescriptorSeeder();
        $seeder->run();
    }

    /**
     * Deleting the widget-data relationship on the db level.
     *
     * @return void
     */
    private function removeWidgetDataRelation()
    {
        /* Adding foreign key to data. */
        Schema::table('widgets', function($table) {
            $table->dropForeign('widgets_data_id_foreign');
        });
        Schema::table('widgets', function($table) {
            $table->dropColumn('data_id');
        });
    }


    /**
     * Reassigning the descriptors.
     *
     * @return void
     */
    private function assignDescriptors()
    {
         DB::connection()->disableQueryLog();

        /* Creating data id, widget_descriptor pairs. */
        foreach (DB::table('data')
            ->join('widget_descriptors', 'widget_descriptors.id', '=', 'data.descriptor_id')
            ->get(array('data.id', 'widget_descriptors.type')) as $data) {
            Log::info("Start mem usage: " . memory_get_usage());

            try {
                self::assignDataToDescriptor($data->id, $data->type);
            } catch (WidgetException $e) {
                Log::warning($e->getMessage());
                DB::table('data')->where('id', $data->id)->delete();
            }

            Log::info("End mem usage: " . memory_get_usage());
        }
         DB::connection()->enableQueryLog();
    }

    /**
     * Assiging a data to the new descriptor, reinitializing if necessary.
     *
     * @param int $dataId
     * @param string $descriptorType
     * @return void
     * @throws WidgetException, ServiceException
     */
    private static function assignDataToDescriptor($dataId, $descriptorType)
    {
        /* Looking for the data in the matching table. */
        if ( ! array_key_exists($descriptorType, self::$matchingTable)) {
            throw new WidgetException('No matching record found for data type ' . $descriptorType . '. Data is being deleted!');
        }
        $descriptorMeta = self::$matchingTable[$descriptorType];
        
        /* Finding the new data descriptor. */
        $dataDescriptor = DataDescriptor::where('category', $descriptorMeta['category'])
            ->where('type', $descriptorMeta['type'])
            ->first(array('id'));
    
        if (is_null($dataDescriptor)) {
            throw new WidgetException('Data descriptor not found. ' . $descriptorType . ' (is the descriptors table seeded?)');
        }
        
        /* Adding the new ID. */    
        DB::table('data')
            ->where('id', $dataId)
            ->update(array('descriptor_id' => $dataDescriptor->id));

        /* At this point the data should be accessible. */
        if (isset($descriptorMeta['reinit']) && $descriptorMeta['reinit'] == true) {
            $memUsed = memory_get_usage();
            Log::info('Requested reinitializtion of data #' . $dataId . ' (' . $descriptorType . ') Memory here: ' . $memUsed);
            try {
                Data::find($dataId, array(
                        'id', 
                        'criteria',
                        'updated_at',
                        'user_id',
                        'descriptor_id',
                        'update_period',
                        'state'
                    ))
                    ->initialize();
            } catch (ServiceException $e) {
                Log::error($e->getMessage());
            }
            Log::info("Done, used memory: " . (memory_get_usage() - $memUsed));
        }
    }

    /**
     * Adding foreign constraint.
     *
     * @return void
     */
    private function addConstraint()
    {
        /* Adding foreign key to data. */
        Schema::table('data', function($table) {
            $table->foreign('descriptor_id')
                  ->references('id')->on('data_descriptors')
                  ->onDelete('cascade');
        });
    }

    /**
     * Removing widget descriptro foreign constraint.
     *
     * @return void
     */
    private function removeConstraint()
    {
        /* Adding foreign key to data. */
        Schema::table('data', function($table) {
            $table->dropForeign('data_descriptor_id_foreign');
        });
    }
}
