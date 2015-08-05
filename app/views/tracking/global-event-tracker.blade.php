<script type="text/javascript">

function trackAll(mode, eventData){
    @if (App::environment('production'))
    
    // Lazy mode
    if (mode == 'lazy') {
        // Google analytics data
        var googleEventData = {
            'ec': eventData['en'],
            'ea': eventData['en'],
            'el': eventData['el'],
            'ev': null,
        };

        // Intercom data
        var intercomEventData = {
            'en': eventData['en'],
            'md': {'metadata': eventData['el']},
        };

        // Mixpanel data
        var mixpanelEventData = {
            'en': eventData['en'],
            'md': {'metadata': eventData['el']},
        };

    // Detailed mode
    } else {
        // Google analytics data
        var googleEventData = {
            'ec': eventData['ec'],
            'ea': eventData['ea'],
            'el': eventData['el'],
            'ev': eventData['ev'],
        };

        // Intercom data
        var intercomEventData = {
            'en': eventData['en'],
            'md': eventData['md'],
        };

        // Mixpanel data
        var mixpanelEventData = {
            'en': eventData['en'],
            'md': eventData['md'],
        };
    };

    // Send events
    ga('send', 'event', 
        googleEventData['ec'],
        googleEventData['ea'],
        googleEventData['el'],
        googleEventData['ev']
    );

    Intercom('trackEvent', 
        intercomEventData['en'],
        intercomEventData['md']
    );

    mixpanel.track(
        mixpanelEventData['en'],
        mixpanelEventData['md']
    );

    @endif

    // Return
    return true;
}

</script>