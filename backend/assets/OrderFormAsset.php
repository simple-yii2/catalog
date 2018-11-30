<?php

namespace cms\catalog\backend\assets;

use yii\web\AssetBundle;

class OrderFormAsset extends AssetBundle
{

	public $js = [
		'order-form.js',
		'order-form-customer.js',
		'order-form-product.js',
		'order-form-delivery.js',
	];

	public $css = [
		'order-form-product.css',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/order-form';
	}

}
