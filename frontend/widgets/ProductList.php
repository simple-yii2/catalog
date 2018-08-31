<?php

namespace cms\catalog\frontend\widgets;

use yii\widgets\ListView;
use cms\catalog\frontend\widgets\assets\ProductListAsset;

class ProductList extends ListView
{

    /**
     * @inheritdoc
     */
    public $layout = '<div class="row">{items}</div>{pager}';

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'product-list'];

    /**
     * @inheritdoc
     */
    public $itemOptions = ['class' => 'col-xs-6 col-sm-4 col-lg-3'];

    /**
     * @var string class name of item widget
     */
    public $itemWidgetClass = 'cms\catalog\frontend\widgets\ProductItem';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->itemView === null) {
            $class = $this->itemWidgetClass;

            $this->itemView = function ($model, $key, $index, $widget) use ($class) {
                return $class::widget(['model' => $model]);
            };
        }

        parent::init();

        ProductListAsset::register($this->view);
    }

}
