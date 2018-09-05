<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use cms\catalog\common\helpers\CurrencyHelper;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\common\models\Product;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\widgets\assets\ProductItemAsset;

class ProductItem extends Widget
{

    /**
     * @var Product
     */
    public $model;

    /**
     * @var array
     */
    public $options = ['class' => 'product-item'];

    /**
     * @var array
     */
    protected $_url;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        ProductItemAsset::register($this->getView());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = $this->model;

        $image = $this->renderImage($model);
        $title = $this->renderTitle($model);
        $price = $this->renderPrice($model);
        $availability = $this->renderAvailability($model);

        $header = Html::tag('span', $image, ['class' => 'product-item-header']);
        $caption = Html::tag('span', $title . $availability . $price, ['class' => 'product-item-caption']);

        echo Html::a($header . $caption, CatalogHelper::createProductUrl($model), ['class' => $this->options]);
    }

    /**
     * Render product image
     * @param Product $model 
     * @return string
     */
    protected function renderImage($model)
    {
        $image = '';
        if (!empty($model->thumb)) {
            $image = Html::img($model->thumb, ['alt' => $model->getTitle()]);
        }

        return Html::tag('span', $image, ['class' => 'product-item-image']);
    }

    /**
     * Render item caption (name, rating and notice)
     * @param Product $model 
     * @return string
     */
    protected function renderTitle($model)
    {
        $name = Html::tag('span', Html::encode($model->name));
        $name .= Html::tag('span', Html::encode($model->model));

        return Html::tag('span', $name, ['class' => 'product-item-title', 'title' => $model->getTitle()]);
    }

    /**
     * Render price and buttons
     * @param Product $model 
     * @return string
     */
    protected function renderPrice($model)
    {
        $currency = CurrencyHelper::getCurrency($model->currency_id);

        //price
        $s = PriceHelper::render('strong', $model->price, $currency);
        $price = Html::tag('span', $s, ['class' => 'product-item-price']);

        return Html::tag('span', $price, ['class' => 'product-item-price-block']);
    }

    /**
     * Render availability
     * @param Product $model 
     * @return string
     */
    protected function renderAvailability($model)
    {
        $s = Html::encode($model->getAvailabilityName());
        return Html::tag('span', $s, ['class' => 'product-item-availability availability' . $model->availability]);
    }

}
