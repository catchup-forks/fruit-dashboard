/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  var canvas  = new FDCanvas(widgetOptions);
  var chartOptions = new FDChartOptions(widgetOptions.page)
  var chartData = window['chartData' + options.id];
  
  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {string} type | the chart type
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(type) {
    // Reinsert canvas
    canvas.reinsert();

    // Draw chart
    switch(type) {
      case 'line':
      default:
          drawLineChart(chartData, chartOptions.getLineChartOptions());
          break;
    }

    // return
    return true;
  }

  /**
   * @function drawLineChart
   * --------------------------------------------------------------------------
   * Draws a line chart
   * @param {dictionary} data | The chart data
   * @param {dictionary} options | The chart options
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function drawLineChart(data, options) {
    // Build data.
    var rawDatasets = data.datasets;
    var transformedData = {
      labels: data.labels,
      datasets: []
    };

    // Transform and push the datasets
    for (i = 0; i < rawDatasets.length; ++i) {
      transformedData.datasets.push(createDataSet(rawDatasets[i].values, rawDatasets[i].name, rawDatasets[i].color));
    }

    // Draw chart.
    new Chart(canvas.get2dContext()).Line(transformedData, options);
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

} // FDChart
