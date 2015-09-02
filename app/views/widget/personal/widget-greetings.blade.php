<h3 id="greeting-{{ $widget->id }}" class="text-white text-center drop-shadow no-margin-top has-margin-vertical-sm truncate reset-line-height">
  Good <span class="greeting"></span>@if(isset(Auth::user()->name)), {{ Auth::user()->name }}@endif!
</h3>
  
  @section('widgetScripts')

   <script type="text/javascript">
     $(document).ready(function() {

      $('#greeting-{{ $widget->id }}').hide();
      
      // fit the greeting on page load
      $('#greeting-{{ $widget->id }}').fitText(2.2, {
        'minFontSize': 24
      });

      $('.greeting').html('{{ SiteConstants::getTimeOfTheDay() }}');

      // bind fittext to a resize event
      $('#greeting-{{ $widget->id }}').bind('resize', function(e){
        $('#greeting-{{ $widget->id }}').fitText(2.2, {
          'minFontSize': 24
        });
      });

        $('#greeting-{{ $widget->id }}').fadeIn(2000);  

    });
   </script>
   
  @append