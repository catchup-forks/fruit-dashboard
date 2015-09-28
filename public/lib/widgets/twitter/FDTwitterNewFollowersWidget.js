/**
 * @class FDTwitterNewFollowersWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterNewFollowers Widget
 * --------------------------------------------------------------------------
 */
function FDTwitterNewFollowersWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDTwitterNewFollowersWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDTwitterNewFollowersWidget.prototype.constructor = FDTwitterNewFollowersWidget;
