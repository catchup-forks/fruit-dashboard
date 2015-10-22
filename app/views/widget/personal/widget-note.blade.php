<div id="note-wrapper-{{ $widget['id'] }}" class="fill-height">
    <textarea id="note-data-{{ $widget['id'] }}" style="width:100%;height:100%;box-sizing:border-box">{{ $widget['data']['text']}}</textarea>
</div>

@section('widgetScripts')

 <script type="text/javascript">
    var widgetData{{ $widget['id'] }} = {
        url: "{{ route('widget.ajax-handler', $widget['id']) }}"
    }
 </script>

@append
