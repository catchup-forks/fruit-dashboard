<h2 class="text-white text-center">
  Good <span class="greeting"></span>@if(isset(Auth::user()->name)), {{ Auth::user()->name }}@endif!
</h2>
  
  @section('widgetScripts')

   <script type="text/javascript">
     $(document).ready(function()
     {
       $('.greeting').html('{{ SiteConstants::getTimeOfTheDay() }}');
     });
   </script>
   
  @append