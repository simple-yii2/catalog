<?php

namespace cms\catalog\backend\assets;

use yii\web\AssetBundle;

class ProductListAsset extends AssetBundle
{

	public $js = [
		'product-list.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/product-list';
	}

}
