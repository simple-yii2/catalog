<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class ProductItemAsset extends AssetBundle
{

    public $css = [
        'product-item' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/product-item';
        parent::init();
    }

}
