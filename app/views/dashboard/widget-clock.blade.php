<li
  data-id='{{ $widget_data["widget_id"] }}' 
  data-row="{{ $widget_data['position']['row'] }}" 
  data-col="{{ $widget_data['position']['col'] }}" 
  data-sizex="{{ $widget_data['position']['x'] }}" 
  data-sizey="{{ $widget_data['position']['y'] }}">

  

  <!--a class='link-button' href='' data-toggle="modal" data-target='#widget-settings-{{ $id }}'><span class="gs-option-widgets"></span></a-->

  <a href="{{ URL::route('connect.deletewidget', $id) }}">
    <span class="fa fa-times fa-inverse position-tr-sm"></span>
  </a>
  
  <div id="digitClock">
  	<h1 id="digitTime" class="no-margin text-white drop-shadow text-center">{{ $currentTime }}</h1>
  </div> <!-- /#digitTime -->
    

</li>

@section('pageModals')
  <!-- clock settings -->
  
  @include('settings.widget-settings')

  <!-- /clock settings -->
@append

@section('widgetScripts')
 <!-- script for clock -->
 <script type="text/javascript">
   $(document).ready(function() {
   	function startTime() {
       var today = new Date();
       var h = today.getHours();
       var m = today.getMinutes();
       m = checkTime(m);
       $('#digitTime').html(h + ':' + m);
       var t = setTimeout(function(){startTime()},500);
     }

     function checkTime(i) {
       if (i<10){i = "0" + i};  // add zero in front of numbers < 10
       return i;
     }

     startTime();

   });

    $(document).ready(function() {
   	 	$('#digitClock').bind('resize', function(e){
   	 	  $('#digitTime').fitText(0.3);
   	 	})
   	});
 </script>
 <!-- /script for clock -->

 
@stop