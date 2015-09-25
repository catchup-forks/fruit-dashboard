/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the gridster elements
 * --------------------------------------------------------------------------
 */
function FDChart(options) {
  // Private variables
  var canvas = $('#' + options['widgetID'] + '-chart')[0];

  // Public functions
  this.draw = draw;

  // initialize
  initialize(options);

  /**
   * @function initialize
   * --------------------------------------------------------------------------
   * Initializes the FDChart object
   * @param {dictionary} options | options
   * @return {true}
   * --------------------------------------------------------------------------
   */
  function initialize(options) {
    if (options['page'] == 'dashboard') {
      setDefaultOptionsDashboard();
    } else if (options['page'] == 'singleStat') {
      setDefaultOptionsSingleStat();
    };

    return this;
  }

  /**
   * @function setDefaultOptionsDashboard
   * --------------------------------------------------------------------------
   * Sets the chart default options for the dashboard page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsDashboard() {
    // remove animation for later implementation
    //Chart.defaults.global.animationSteps = 60;
    //Chart.defaults.global.animationEasing = "easeOutQuart";
    Chart.defaults.global.showScale = false;
    Chart.defaults.global.showTooltips = false;
    Chart.defaults.global.responsive = false;
    
    // Return
    return true;
  }

  /**
   * @function getLineChartOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the line chart options for the dashboard page
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsDashboard() {
    return {
       pointDot: false,
       bezierCurve: true,
       bezierCurveTension : 0.35,
       animation: false
    };
  }

  /**
   * @function setDefaultOptionsSingleStat
   * --------------------------------------------------------------------------
   * Sets the chart default options for the single stat page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsSingleStat() {
    Chart.defaults.global.animationSteps = 60;
    Chart.defaults.global.animationEasing = "easeOutQuart";
    Chart.defaults.global.tooltipCornerRadius = 4;
    Chart.defaults.global.tooltipXPadding = 5;
    Chart.defaults.global.tooltipYPadding = 5;
    Chart.defaults.global.tooltipCaretSize = 5;
    Chart.defaults.global.tooltipFillColor = "rgba(0,0,0,0.6)";
    Chart.defaults.global.tooltipFontSize = 11;
    Chart.defaults.global.scaleLineColor = "rgba(179,179,179,1)";
    Chart.defaults.global.scaleFontSize = 9;
    Chart.defaults.global.scaleFontColor = "rgba(230,230,230,1)";
    
    // Return
    return true;
  }

  /**
   * @function getLineChartOptionsSingleStat
   * --------------------------------------------------------------------------
   * Returns the line chart options for the single stat page
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsSingleStat() {
    return {
       pointHitDetectionRadius : 5,
       pointDotRadius : 2,
       scaleGridLineColor : "rgba(179,179,179,0.4)",
       scaleGridLineWidth : 0.35,
       tooltipTemplate: "<%if (label){%><%=label %>: <%}%><%= value %>",
       multiTooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel %>: <%}%><%= value %>",
    };
  }

  /**
   * @function createDataSet
   * --------------------------------------------------------------------------
   * Creates a dataset for the chart
   * @return {dictionary} the generated dataset
   * --------------------------------------------------------------------------
   */
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

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {dictionary} options |the chart options
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(options, data) {
    switch(options['type']) {
      case 'line':
      default:
          drawLineChart(data);
          break;
    }

    // return
    return true;
  }

  /**
   * @function drawLineChart
   * --------------------------------------------------------------------------
   * Draws a line chart
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function drawLineChart(data) {
    // Build data.
    var rawDatasets = data['datasets'];
    var chartData = {
      labels: data['labels'],
      datasets: []
    };

    // Transform and push the datasets
    for (i = 0; i < rawDatasets.length; ++i) {
      chartData.datasets.push(createDataSet(rawDatasets[i]['values'], rawDatasets[i]['name'], rawDatasets[i]['color']));
    }

    // Draw chart.
    new Chart(canvas.getContext("2d")).Line(chartData, getLineChartOptionsDashboard());
  }

} // FDChart
