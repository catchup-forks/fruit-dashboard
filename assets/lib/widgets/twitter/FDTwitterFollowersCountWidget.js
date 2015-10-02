/**
 * @class FDTwitterFollowersCountWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterFollowersCount Widget
 * --------------------------------------------------------------------------
 */
var FDTwitterFollowersCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Automatically initialize
 this.init();
};

FDTwitterFollowersCountWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTwitterFollowersCountWidget.prototype.constructor = FDTwitterFollowersCountWidget;

