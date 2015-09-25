/**
 * @class FDChartOptions
 * --------------------------------------------------------------------------
 * Class function to set the global chart options
 * --------------------------------------------------------------------------
 */
function FDChartOptions(pageName) {
  // Private variables
  var page = pageName;
  
  // Public functions
  this.getLineChartOptions = getLineChartOptions;

  // Initialize automatically
  initialize();

  /**
   * @function initialize
   * --------------------------------------------------------------------------
   * Initializes the FDChartOptions object
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function initialize() {
    if (page == 'dashboard') {
      setDefaultOptionsDashboard();
    } else if (page == 'singleStat') {
      setDefaultOptionsSingleStat();
    };

    return this;
  }

  /**
   * @function getLineChartOptions
   * --------------------------------------------------------------------------
   * Returns the options for a line chart based on the page
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptions() {
    if (page == 'dashboard') {
      return getLineChartOptionsDashboard();
    } else if (page == 'singleStat') {
      return getLineChartOptionsSingleStat();
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
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsDashboard() {
    return {
       pointDot: false,
       bezierCurve: true,
       bezierCurveTension : 0.35,
       animation: true
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
    Chart.defaults.global.animationSteps      = 60;
    Chart.defaults.global.animationEasing     = "easeOutQuart";
    Chart.defaults.global.tooltipCornerRadius = 4;
    Chart.defaults.global.tooltipXPadding     = 5;
    Chart.defaults.global.tooltipYPadding     = 5;
    Chart.defaults.global.tooltipCaretSize    = 5;
    Chart.defaults.global.tooltipFillColor    = "rgba(0,0,0,0.6)";
    Chart.defaults.global.tooltipFontSize     = 11;
    Chart.defaults.global.scaleLineColor      = "rgba(179,179,179,1)";
    Chart.defaults.global.scaleFontSize       = 9;
    Chart.defaults.global.scaleFontColor      = "rgba(230,230,230,1)";
    
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

} // FDChartOptions
