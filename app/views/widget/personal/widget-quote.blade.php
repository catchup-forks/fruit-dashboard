<div class="text-white text-center drop-shadow quote">
  <div class="margin-top-sm has-margin-horizontal">
    <p class="lead body" id="quote-{{ $widget->id }}"></p>
    <p class="source" id="author-{{ $widget->id }}"></p>
  </div> <!-- /.container -->
</div>

@section('widgetScripts')
<script type="text/javascript">
  var widgetData{{ $widget->id }} = {
      author: "{{ $widget->getData()['author'] }}",
      quote: "{{ $widget->getData()['quote'] }}"
  }
</script>
@append