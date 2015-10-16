/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var options      = widgetOptions;
  var canvas       = new FDCanvas(options);
  var chartOptions = new FDChartOptions(options)

  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {string} type | the chart type
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function draw(type, data) {

    // In case of one point or flat datasets, render with different options.
    var singlePointOptions = {};
    // var start;
    // var min;
    // var max;
    // var steps;

    // If on dashboard and datasets exist.
    if (data.datasets && widgetOptions.data.page == 'dashboard') {
      // In case of one point datasets, unshift an extra label.
      if (data.datasets[0].values.length == 1) {
        data.labels.unshift(data.labels[0]);
        
        // Build extra options for single point charts.
        singlePointOptions = {
          pointDotStrokeWidth : 1,
          pointDotRadius : 3,
        }
      };

      // For each one point dataset, unshift an extra value.
      for (var i = data.datasets.length - 1; i >= 0; i--) {

        if (data.datasets[i].values.length == 1) {
          data.datasets[i].values.unshift(data.datasets[i].values[0]);
        };

        // If Math operations are needed on datasets, do it here.
        // min = Math.min(data.datasets[i].values);
        // max = Math.max(data.datasets[i].values);
        // if (min == max) {
        //   singlePointOptions = {
        //     showScale : true,
        //     scaleOverride : true,
        //     scaleSteps: Math.ceil((max-start)/step),
        //     scaleStepWidth: step,
        //     scaleStartValue: start,
        //   }
        // };
        
      };

    };

    // Clear the existing chart
    clear();

    // Draw chart
    switch(type) {
      case 'line':
        var canvasContext = canvas.get2dContext();
        if (canvasContext) {
          new Chart(canvasContext).Line(
            chartOptions.transformLineChartDatasets(data), 
            chartOptions.getLineChartOptions(singlePointOptions))
        };
        break;
      case 'combined':
        // in datasets set type = line for line
        // if not set, defaults to bar chart
        var canvasContext = canvas.get2dContext();
        if (canvasContext) {
          new Chart(canvasContext).Overlay(
            chartOptions.transformLineChartDatasets(data), 
            chartOptions.getLineChartOptions(singlePointOptions))
        };
        break;
      default:
          var canvasContext = canvas.get2dContext();
          if (canvasContext) {
            new Chart(canvasContext).Line(
              chartOptions.transformLineChartDatasets(data), 
              chartOptions.getLineChartOptions(singlePointOptions))
          };
          break;
    }

    // return
    return this;
  }

  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Clears the previous chart
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function clear() {
    // Reinsert canvas
    canvas.reinsert();
  }

} // FDChart
