/**
 * @class FDGlobalChartOptions
 * --------------------------------------------------------------------------
 * Class function to set the global chart options
 * --------------------------------------------------------------------------
 */
function FDGlobalChartOptions(widgetOptions) {
  // Private variables
  var page = widgetOptions.data.page;
  
  // Public functions
  this.init = init;

  /**
   * @function init
   * --------------------------------------------------------------------------
   * Initializes the FDGlobalChartOptions object
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function init() {
    // Set default options
    if (page == 'dashboard') {
      setDefaultOptionsDashboard();
    } else if (page == 'singlestat') {
      setDefaultOptionsSingleStat();
    };
    // Initialize tooltips
    initializeTooltips();
  }

  /**
   * @function initializeTooltips
   * --------------------------------------------------------------------------
   * Initializes the Chart tooltip
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function initializeTooltips() {
    Chart.defaults.global.customTooltips = function(tooltip) {
      // Hide if no tooltip
      if (!tooltip) {
        tooltipEl.css({ opacity: 0 });
        tooltipOverviewEl.css({ opacity: 0 });
        return;
      }

      // Get tooltip elements
      var tooltipEl = $('#chartjs-tooltip');
      var tooltipOverviewEl = $('#chartjs-tooltip-overview');
      
      // Set caret Position
      tooltipEl.removeClass('above below');
      tooltipEl.addClass(tooltip.yAlign);
      // Set Text
      tooltipEl.html(tooltip.text);

      // Find Y Location on page
      if (tooltip.yAlign == 'above') {
        var top = tooltip.y - tooltip.caretHeight - tooltip.caretPadding;
      } else {
        var top = tooltip.y + tooltip.caretHeight + tooltip.caretPadding;
      }
      // Display, position, and set styles for font
      tooltipEl.css({
        opacity: 1,
        left: tooltip.chart.canvas.offsetLeft + tooltip.x + 'px',
        top: tooltip.chart.canvas.offsetTop + top + 'px',
        fontFamily: tooltip.fontFamily,
        fontSize: tooltip.fontSize,
        fontStyle: tooltip.fontStyle,
      });

      tooltipOverviewEl.css({
        opacity: 1,
        fontFamily: tooltip.fontFamily,
        fontSize: tooltip.fontSize,
        fontStyle: tooltip.fontStyle,
      });
    };

    // Return
    return this;
  }

  /**
   * @function setDefaultOptionsDashboard
   * --------------------------------------------------------------------------
   * Sets the chart default options for the dashboard page
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsDashboard() {
    Chart.defaults.global.responsive         = false;
    Chart.defaults.global.animation.duration = 0;

    // Return
    return this;
  }

  /**
   * @function setDefaultOptionsSingleStat
   * --------------------------------------------------------------------------
   * Sets the chart default options for the single stat page
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsSingleStat() {
    Chart.defaults.global.responsive      = false;
    
    // Return
    return this;
  }
} // FDGlobalChartOptions
