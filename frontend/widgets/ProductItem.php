<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Product;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\helpers\CurrencyHelper;
use cms\catalog\frontend\helpers\PriceHelper;
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
	 * @var array of string or Closure
	 */
	public $buttons = [];

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

		$thumb = $this->renderThumb($model);
		$caption = $this->renderCaption($model);
		$controls = $this->renderControls($model);

		echo Html::tag('div', $thumb . $caption . $controls, ['class' => $this->options]);
	}

	/**
	 * Create url for product view
	 * @param Product $model 
	 * @return array
	 */
	protected function createUrl($model)
	{
		if ($this->_url !== null)
			return $this->_url;

		return $this->_url = CatalogHelper::createProductUrl($model);
	}

	/**
	 * Render product thumb
	 * @param Product $model 
	 * @return string
	 */
	protected function renderThumb($model)
	{
		//image
		$image = '';
		if (!empty($model->thumb))
			$image = Html::a(Html::img($model->thumb), $this->createUrl($model));

		return Html::tag('div', $image, ['class' => 'product-thumb']);
	}

	/**
	 * Render item caption (name, rating and notice)
	 * @param Product $model 
	 * @return string
	 */
	protected function renderCaption($model)
	{
		//name
		$s = $model->name;
		if (!empty($model->model)) {
			$s .= ' ' . $model->model;
		}
		$name = Html::tag('div', Html::a(Html::encode($s), $this->createUrl($model)), ['class'=>'product-name']);

		//rating
		$rating = '';

		//notice
		$notice = '';
		// $notice = Html::tag('div', Html::a(Html::encode($model->notice), $url), ['product-notice']);

		return Html::tag('div', $name . $rating . $notice, ['class' => 'product-caption']);
	}

	/**
	 * Render price and buttons
	 * @param Product $model 
	 * @return string
	 */
	protected function renderControls($model)
	{
		$formatter = Yii::$app->getFormatter();
		$currency = CurrencyHelper::getCurrency($model->currency_id);

		//old price
		$s = '';
		if (!empty($model->oldPrice))
			$s = PriceHelper::render('s', $model->oldPrice, $currency);
		$oldPrice = Html::tag('div', $s, ['class' => 'product-old-price']);

		//price
		$s = PriceHelper::render('span', $model->price, $currency);
		$price = Html::tag('div', $s, ['class' => 'product-price']);

		//buttons
		$buttons = '';
		foreach ($this->buttons as $button) {
			$buttons .= $this->renderButton($button);
		}
		if (!empty($buttons)) {
			$buttons = Html::tag('div', $buttons, ['class' => 'product-buttons']);
		}

		//available
		$available = '';

		return Html::tag('div', $oldPrice . $price . $buttons . $available, ['class' => 'product-controls']);
	}

	protected function renderButton($button)
	{
		if (is_callable($button)) {
			return call_user_func($button, $this->model);
		}

		return $button;
	}

}
