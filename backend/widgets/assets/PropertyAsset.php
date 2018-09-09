<?php

namespace cms\catalog\backend\widgets\assets;

use yii\web\AssetBundle;

class PropertyAsset extends AssetBundle
{

    public $js = [
        'property' . (YII_DEBUG ? '' : '.min') . '.js',
    ];

    public $css = [
        'property' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/property';
        parent::init();
    }

}
