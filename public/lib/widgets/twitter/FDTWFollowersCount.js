/**
 * @class FDTwitterFollowersCountWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterFollowersCount Widget
 * --------------------------------------------------------------------------
 */
var FDTwitterFollowersCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDCountWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDTwitterFollowersCountWidget.prototype = Object.create(FDCountWidget.prototype);
FDTwitterFollowersCountWidget.prototype.constructor = FDTwitterFollowersCountWidget;
