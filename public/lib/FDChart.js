/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var canvas       = new FDCanvas(widgetOptions.selector);
  var chartOptions = new FDChartOptions(widgetOptions.page)

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
    // Clear the existing chart
    clear();

    // Draw chart
    switch(type) {
      case 'line':
      default:
          if (canvas.get2dContext()) {
            new Chart(canvas.get2dContext()).Line(
              chartOptions.transformLineChartDatasets(data), 
              chartOptions.getLineChartOptions())
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
