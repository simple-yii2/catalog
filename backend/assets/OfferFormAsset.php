<?php

namespace cms\catalog\backend\assets;

use yii\web\AssetBundle;

class OfferFormAsset extends AssetBundle
{

	public $js = [
		'offer-form.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/offer-form';
	}

}
