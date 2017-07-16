<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class OfferFilterAsset extends AssetBundle
{

	public $css = [
		'offer-filter.css',
	];

	public $js = [
		'offer-filter.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/offer-filter';
		parent::init();
	}

}
