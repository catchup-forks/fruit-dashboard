/**
 * @class FDTwitterFollowersWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterFollowers Widget
 * --------------------------------------------------------------------------
 */
function FDTwitterFollowersWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDTwitterFollowersWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDTwitterFollowersWidget.prototype.constructor = FDTwitterFollowersWidget;
