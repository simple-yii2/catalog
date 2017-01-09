<?php

namespace cms\catalog\backend\widgets\assets;

use yii\web\AssetBundle;

class PropertyAsset extends AssetBundle
{

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->sourcePath = __DIR__ . '/property';

		$this->js = ['property.js'];

		$this->depends = ['yii\web\JqueryAsset'];
	}

}
