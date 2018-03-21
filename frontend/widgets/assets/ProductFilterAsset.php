<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class ProductFilterAsset extends AssetBundle
{

	public $css = [
		'product-filter.css',
	];

	public $js = [
		'product-filter.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/product-filter';
		parent::init();
	}

}
