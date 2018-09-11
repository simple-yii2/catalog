<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class ProductToolbarAsset extends AssetBundle
{

    public $css = [
        'product-toolbar' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public $js = [
        'product-toolbar' . (YII_DEBUG ? '' : '.min') . '.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/product-toolbar';
        parent::init();
    }

}
