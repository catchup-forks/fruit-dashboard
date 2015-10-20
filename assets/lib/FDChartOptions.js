/**
 * @class FDChartOptions
 * --------------------------------------------------------------------------
 * Class function to set the global chart options
 * --------------------------------------------------------------------------
 */
function FDChartOptions(widgetOptions) {
  // Private variables
  var page = widgetOptions.data.page;
  
  // Public functions
  this.init = init;
  this.getLineChartOptions = getLineChartOptions;
  this.transformLineChartDatasets = transformLineChartDatasets;

  /**
   * @function init
   * --------------------------------------------------------------------------
   * Initializes the FDChartOptions object
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function init() {
    if (page == 'dashboard') {
      setDefaultOptionsDashboard();
    } else if (page == 'singlestat') {
      setDefaultOptionsSingleStat();
    };

    return this;
  }

  /**
   * @function getLineChartOptions
   * --------------------------------------------------------------------------
   * Returns the options for a line chart based on the page
   * @param {object} type | extra options for single point or flat line charts
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptions(singlePointOptions) {
    if (page == 'dashboard') {
      return getLineChartOptionsDashboard(singlePointOptions);
    } else if (page == 'singlestat') {
      return getLineChartOptionsSingleStat();
    }
  }

  /**
   * @function transformLineChartDatasets
   * --------------------------------------------------------------------------
   * Returns the options for a line chart based on the page
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function transformLineChartDatasets(data) {
    if (page == 'dashboard') {
      return transformLineChartDatasetsDashboard(data);
    } else if (page == 'singlestat') {
      // FIXME
      return transformLineChartDatasetsDashboard(data);
    }
  }

  /**
   * @function setDefaultOptionsDashboard
   * --------------------------------------------------------------------------
   * Sets the chart default options for the dashboard page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsDashboard() {
    //Chart.defaults.global.animationSteps  = 60;
    //Chart.defaults.global.animationEasing = "easeOutQuart";
    Chart.defaults.global.showScale       = false;
    Chart.defaults.global.showTooltips    = false;
    Chart.defaults.global.responsive      = false;
    
    // Return
    return true;
  }

  /**
   * @function getLineChartOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the line chart options for the dashboard page
   * @param {object} type | extra options for single point or flat line charts
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsDashboard(singlePointOptions) {
    var defaultOptions = {
      pointDot: true,
      pointDotRadius : 1.2,
      bezierCurve: true,
      bezierCurveTension : 0.35,
      animation: false
    }

    if (singlePointOptions) {
      jQuery.extend(defaultOptions, singlePointOptions);
    };

    return defaultOptions;
  }

  /**
   * @function transformLineChartDatasetsDashboard
   * --------------------------------------------------------------------------
   * Creates a dataset for the chart
   * @param {dictionary} data | The chart data
   * @return {dictionary} the generated dataset
   * --------------------------------------------------------------------------
   */
  function transformLineChartDatasetsDashboard(data) {
    var transformedData = {
      labels  : data.labels,
      datasets: [],
    };

    for (i = 0; i < data.datasets.length; ++i) {
      transformedData.datasets.push(
          transform(
            data.datasets[i].values, 
            data.datasets[i].name, 
            data.datasets[i].color
          )
      );
    }

    // Return
    return transformedData;

    function transform(values, name, color) {
      return {
        label: name,
        fillColor : "rgba(" + color + ", 0.2)",
        strokeColor : "rgba(" + color + ", 1)",
        pointColor : "rgba(" + color + ", 1)",
        pointStrokeColor : "#fff",
        pointHighlightFill : "#fff",
        pointHighlightStroke : "rgba(" + color + ", 1)",
        data: values
      }
    }
  }

  /**
   * @function setDefaultOptionsSingleStat
   * --------------------------------------------------------------------------
   * Sets the chart default options for the single stat page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsSingleStat() {
    Chart.defaults.global.animationSteps      = 60;
    Chart.defaults.global.animationEasing     = "easeOutQuart";
    Chart.defaults.global.tooltipCornerRadius = 4;
    Chart.defaults.global.tooltipXPadding     = 5;
    Chart.defaults.global.tooltipYPadding     = 5;
    Chart.defaults.global.tooltipCaretSize    = 5;
    Chart.defaults.global.tooltipFillColor    = "rgba(0,0,0,0.6)";
    Chart.defaults.global.tooltipFontSize     = 11;
    //Chart.defaults.global.scaleLineColor      = "rgba(179,179,179,1)";
    Chart.defaults.global.scaleFontSize       = 9;
    //Chart.defaults.global.scaleFontColor      = "rgba(230,230,230,1)";
    
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
       //pointHitDetectionRadius : 5,
       //pointDotRadius : 2,
       //scaleGridLineColor : "rgba(179,179,179,0.4)",
       //scaleGridLineWidth : 0.35,
       tooltipTemplate: function (d) {
          if (d.label) {
            return d.label + ': ' + d.value;
          } else {
            return d.value;
          };
       },
       multiTooltipTemplate: function (d) {
          if (d.datasetLabel) {
            return d.datasetLabel + ': ' + d.value;
          } else {
            return d.value;
          };
       }
    };
  }

} // FDChartOptions
