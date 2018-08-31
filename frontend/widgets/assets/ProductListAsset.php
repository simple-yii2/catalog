<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class ProductListAsset extends AssetBundle
{

    public $css = [
        'product-list' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/product-list';
        parent::init();
    }

}
