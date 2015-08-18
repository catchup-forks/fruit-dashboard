<?php

/**
 * Routes for development pages
 * @see DevelopmentController
 */
if (!App::environment('production')) {

    Route::group([
            'prefix'    => 'dev',
        ], function() {
    });
    
}
