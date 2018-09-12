<?php

namespace cms\catalog\frontend\widgets\assets;

use yii\web\AssetBundle;

class CategoriesAsset extends AssetBundle
{

    public $css = [
        'categories' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/categories';
        parent::init();
    }

}
