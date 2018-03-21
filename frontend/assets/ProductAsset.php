<?php

namespace cms\catalog\frontend\assets;

use yii\web\AssetBundle;

class ProductAsset extends AssetBundle
{

	public $css = [
		'product.css',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/product';

		parent::init();
	}

}
