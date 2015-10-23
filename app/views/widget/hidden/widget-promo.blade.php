<h4 id="promo-{{ $widget['id'] }}" class="no-margin-top has-margin-vertical-sm">
  <a href="#" onclick="setLocation{{ $widget['id'] }}()">
    {{ HTML::image(
      $widget['settings']['photo_location'],
      $widget['relatedDescriptor']->name, array(
        'class' => 'promo-opaque img-responsive img-rounded center-block'
      )
    )}}
  </a>
</h4>

@section('widgetScripts')
<script type="text/javascript">
  function setLocation{{ $widget['id'] }}(){
    url = "{{ $widget['connectionMeta']['url'] }}?createDashboard=1";
    if (window!=window.top) {
      window.open(url, '_blank');
    } else {
      window.location = url;
    }
  }
</script>
@append