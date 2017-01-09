<?php

namespace cms\catalog\backend\assets;

use yii\web\AssetBundle;

class GoodsFormAsset extends AssetBundle
{

	public $js = [
		'goods-form.js',
	];
	
	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/goods-form';
	}

}
