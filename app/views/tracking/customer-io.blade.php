<script type="text/javascript">
  var _cio = _cio || [];

  (function() {
    var a,b,c;a=function(f){return function(){_cio.push([f].
    concat(Array.prototype.slice.call(arguments,0)))}};b=["identify",
    "track"];for(c=0;c<b.length;c++){_cio[b[c]]=a(b[c])};
    var t = document.createElement('script'),
        s = document.getElementsByTagName('script')[0];
    t.async = true;
    t.id    = 'cio-tracker';
    t.setAttribute('data-site-id', '{{ $_ENV["CUSTOMER_IO_SITE_ID"] }}');
    t.src = 'https://assets.customer.io/assets/track.js';
    s.parentNode.insertBefore(t, s);
  })();
</script>

<script type="text/javascript">
  @if (Auth::user())
    _cio.identify({
      id:         '{{ Auth::user()->id }}',
      email:      '{{ Auth::user()->email }}',
      created_at: {{ strtotime(Auth::user()->created_at) }},
      name:       '{{ Auth::user()->name }}',
      plan:       '{{ Auth::user()->subscription->plan->name }}'
    });
  @else
    _cio.identify({
      id:         '0', 
      email:      'anonymous@user.com',
      created_at: {{ Carbon::now()->timestamp }}, // seconds since the epoch (January 1, 1970)
      name:       'Anonymous user',
      plan:       '{{ Plan::getFreePlan()->name }}'
    });
  @endif
</script>