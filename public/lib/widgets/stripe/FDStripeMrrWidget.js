/**
 * @class FDStripeMrrWidget
 * --------------------------------------------------------------------------
 * Class function for the StripeMrr Widget
 * --------------------------------------------------------------------------
 */
function FDStripeMrrWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDStripeMrrWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDStripeMrrWidget.prototype.constructor = FDStripeMrrWidget;

