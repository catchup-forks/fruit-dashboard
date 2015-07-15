<h2 class="text-white text-center">
  Good 
  <span class="greeting"></span>

  @if(isset(Auth::user()->name)), {{ Auth::user()->name }}@endif!
</h2>
  
  @section('widgetScripts')

   <script type="text/javascript">
     $(document).ready(function()
     {
       var hours = new Date().getHours();
       
       if(17 <= hours || hours < 5) { $('.greeting').html('evening'); }
       if(5 <= hours && hours < 13) { $('.greeting').html('morning'); }
       if(13 <= hours && hours < 17) { $('.greeting').html('afternoon'); } 
     });
   </script>
   
  @append