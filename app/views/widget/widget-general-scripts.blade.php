<script type="text/javascript">

  // Overriding chartjs defaults.
  Chart.defaults.global.animationSteps = 50;
  Chart.defaults.global.tooltipYPadding = 16;
  Chart.defaults.global.tooltipCornerRadius = 0;
  Chart.defaults.global.tooltipTitleFontStyle = "normal";
  Chart.defaults.global.tooltipFillColor = "rgba(160,160,160,0.8)";
  Chart.defaults.global.animationEasing = "easeOutBounce";
  Chart.defaults.global.responsive = true;
  Chart.defaults.global.scaleLineColor = "black";
  Chart.defaults.global.scaleFontSize = 9;

  var chartOptions = {
     responsive: true,
     pointHitDetectionRadius : 2,
     pointDotRadius : 3,
     bezierCurve: false,
     scaleShowVerticalLines: false,
     tooltipTemplate: "<%if (label){%><%=label %>: <%}%>$<%= value %>",
     animation: false
  };

  $(".deleteWidget").click(function(e) {

    e.preventDefault();

    // initialize url
    var url = "{{ route('widget.delete', 'widgetID') }}".replace('widgetID', $(this).attr("data-id"))

    // Look for the actual gridster dashboard instance.
    var gridsterID = $(this).closest('.gridster').attr('id');
    var regridster

    // Reinitialize gridster and remove widget.
    regridster = $('#' + gridsterID + ' ul').gridster().data('gridster');
    regridster.remove_widget($(this).closest('li'));


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

  function sendAjax(postData, widgetId, callback) {
    $.ajax({
    type: "POST",
    data: postData,
    url: "{{ route('widget.ajax-handler', 'widgetID') }}".replace("widgetID", widgetId)
    }).done(function( data ) {
      callback(data);
    });
  }

  function loadWidget(widgetId, callback) {
    var done = false;
    function pollState() {
      sendAjax({'state_query': true}, widgetId, function (data) {
        if (data['ready']) {
          $("#widget-loading-" + widgetId).hide();
          $("#widget-wrapper-" + widgetId).show();
          done = true;
        }
        callback(data['data']);
      });
    }
    if ( ! done ) {
      setTimeout(pollState, 2000);
    }
  };

  function refreshWidget(widgetId, callback) {
    $("#widget-wrapper-" + widgetId).hide();
    $("#widget-loading-" + widgetId).show();
    sendAjax({'refresh_data': true}, widgetId, callback);
    loadWidget(widgetId, callback);
  };

  function createDataSet(values, name, color) {
    return {
      label: name,
      fillColor : "rgba(" + color + ",0.2)",
      strokeColor : "rgba(" + color + ",1)",
      pointColor : "rgba(" + color + ",1)",
      pointStrokeColor : "#fff",
      pointHighlightFill : "#fff",
      pointHighlightStroke : "rgba(" + color + ",1)",
      data: values
    }
  }
  function drawLineGraph(canvas, datasets, labels, name) {
    // Building data.
    var chartData = {
      labels: labels,
      datasets: []
    };
    for (i = 0; i < datasets.length; ++i) {
      if (datasets[i]['color']) {
        color = datasets[i]['color'];
      } else {
        color = '{{ SiteConstants::getChartJsColors()[0] }}';
      }
      chartData.datasets.push(createDataSet(datasets[i]['values'], datasets[i]['name'], color));
    }

    // Getting context.
    var ctx = canvas[0].getContext("2d");

    // Drawing chart.
    var chart = new Chart(ctx).Line(chartData, chartOptions);
  }
</script>