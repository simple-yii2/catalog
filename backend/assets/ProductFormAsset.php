<?php

namespace cms\catalog\backend\assets;

use yii\web\AssetBundle;

class ProductFormAsset extends AssetBundle
{

	public $js = [
		'product-form.js',
	];

	public $css = [
		'product-form.css',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/product-form';
	}

}
