<?php

namespace cms\catalog\frontend\assets;

use yii\web\AssetBundle;

class OfferAsset extends AssetBundle
{

	public $css = [
		'offer.css',
	];

	public function init()
	{
		$this->sourcePath = __DIR__ . '/offer';

		parent::init();
	}

}
