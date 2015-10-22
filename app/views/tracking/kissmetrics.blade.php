<script type="text/javascript">
    var _kmq = _kmq || [];
    var _kmk = _kmk || "{{ $_ENV['KISSMETRICS_KEY'] }}";
    function _kms(u){
     setTimeout(function(){
       var d = document, f = d.getElementsByTagName('script')[0],
       s = d.createElement('script');
       s.type = 'text/javascript'; s.async = true; s.src = u;
       f.parentNode.insertBefore(s, f);
     }, 1);
    }
    _kms('//i.kissmetrics.com/i.js');
    _kms('//scripts.kissmetrics.com/' + _kmk + '.2.js');
</script>

<script type="text/javascript">
    @if (Auth::user())
        _kmq.push(['identify', '{{ Auth::user()->email }}']);
        _kmq.push(['set', {
            'name':  '{{ Auth::user()->name }}',
            'is_extension_installed': (window!=window.top) ? true : false
        }]);
    @else
        _kmq.push(['identify', 'anonymous@user.com']);
    @endif
</script>