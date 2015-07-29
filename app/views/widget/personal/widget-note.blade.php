<div id="note-wrapper">
    <textarea id="note-data" style="width:100%;height:100%;box-sizing:border-box">{{ $widget->getData()}}</textarea>
</div>

@section('widgetScripts')

 <script type="text/javascript">
   $(document).ready(function() {
    $("#note-data").change(function () {
        $.ajax({
            type: "POST",
            data: {'text': $("#note-data").val()},
            url: "{{ route('widget.ajax-handler', $widget->id) }}"
        });
    });
   });
 </script>

@append
