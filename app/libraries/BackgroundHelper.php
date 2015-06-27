<?php

class BackgroundHelper {

	public static function getConnectPageWidgetData(){
		$widgetData = [
			'provider' => 'background',
			'caption' => 'Background settings',
			'icon' => 'fa-picture-o',
			'premium' => false,
		];
		return $widgetData;
	} # / function getConnectPageWidgetData


	public static function wizard($step = NULL){
		return Redirect::route('connect.editwidget', 'background');
	} # / function wizard

}