<?php

namespace swilson1337\sidenav;

class SideNavAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/swilson1337/yii2-widget-sidenav/assets';
	
	public $css = [
		'css/sidenav.css',
	];
	
	public $js = [
		'js/sidenav.js',
	];
}
