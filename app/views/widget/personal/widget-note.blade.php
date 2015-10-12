<div id="note-wrapper-{{ $widget['id'] }}" class="fill-height">
    <textarea id="note-data-{{ $widget['id'] }}" style="width:100%;height:100%;box-sizing:border-box">{{ $widget['data']['text']}}</textarea>
</div>

@section('widgetScripts')

 <script type="text/javascript">
   $(document).ready(function() {
    $("#note-data-{{ $widget['id'] }}").on('change keyup', function () {
        $.ajax({
            type: "POST",
            data: {'text': $("#note-data-{{ $widget['id'] }}").val()},
            url: "{{ route('widget.ajax-handler', $widget['id']) }}"
        });
    });
   });
 </script>

@append
