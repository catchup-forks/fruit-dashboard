<script type="text/javascript">
  $(".deleteWidget").click(function(e) {
    e.preventDefault();

    // initialize url
    var url = "{{ route('widget.delete', 'widgetID') }}".replace('widgetID', $(this).attr("data-id"))

    // Remove widget visually
    $(this).parent().remove();

    // Call ajax function
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: url,
           data: null,
           success: function(data) {
              $.growl.notice({
                title: "Success!",
                message: "You successfully deleted the widget",
                size: "large",
                duration: 3000,
                location: "br"
              });
           },
           error: function(){
              $.growl.error({
                title: "Error!",
                message: "Something went wrong, we couldn't delete your widget. Please try again.",
                size: "large",
                duration: 3000,
                location: "br"
              });
           }
    });
  });
</script>