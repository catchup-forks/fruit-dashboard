<script type="text/javascript">
    (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
</script>

<script type="text/javascript">
  window.intercomSettings = {
    app_id: '{{ $_ENV["INTERCOM_APP_ID"] }}',
    @if (Auth::user())
        user_id: {{ Auth::user()->id }},
        name: '{{ Auth::user()->name }}',
        email: '{{ Auth::user()->email }}',
        created_at: {{ strtotime(Auth::user()->created_at) }},
        "is_extension_installed": (window!=window.top) ? true : false,
        "startup_type": "{{ Auth::user()->settings->startup_type }}",
        "project_name": "{{ Auth::user()->settings->project_name }}",
        "project_url": "{{ Auth::user()->settings->project_url }}",
        "company_size": "{{ Auth::user()->settings->company_size }}",
        "company_funding": "{{ Auth::user()->settings->company_funding }}"
    @else
        user_id: 0,
        name: 'Anonymous user',
        email: 'anonymous@user.com',
        created_at: {{ Carbon::now()->timestamp }},
    @endif
  };
</script>