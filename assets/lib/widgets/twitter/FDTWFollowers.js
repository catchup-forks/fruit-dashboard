/**
 * @class FDTwitterFollowersWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterFollowers Widget
 * --------------------------------------------------------------------------
 */
function FDTwitterFollowersWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDTwitterFollowersWidget.prototype = Object.create(FDVisualizer.prototype);
FDTwitterFollowersWidget.prototype.constructor = FDTwitterFollowersWidget;
