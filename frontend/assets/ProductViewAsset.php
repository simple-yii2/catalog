<?php

namespace cms\catalog\frontend\assets;

use yii\web\AssetBundle;

class ProductViewAsset extends AssetBundle
{

    public $css = [
        'product-view' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/product-view';

        parent::init();
    }

}
