<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Offer;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\helpers\PriceHelper;
use cms\catalog\frontend\widgets\assets\OfferItemAsset;

class OfferItem extends Widget
{

	/**
	 * @var Offer
	 */
	public $model;

	/**
	 * @var array
	 */
	public $options = ['class' => 'offer-item'];

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
		OfferItemAsset::register($this->getView());
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
	 * Create url for offer view
	 * @param Offer $model 
	 * @return array
	 */
	protected function createUrl($model)
	{
		if ($this->_url !== null)
			return $this->_url;

		return $this->_url = CatalogHelper::createOfferUrl($model);
	}

	/**
	 * Render offer thumb
	 * @param Offer $model 
	 * @return string
	 */
	protected function renderThumb($model)
	{
		//image
		$image = '';
		if (!empty($model->thumb))
			$image = Html::a(Html::img($model->thumb), $this->createUrl($model));

		return Html::tag('div', $image, ['class' => 'offer-thumb']);
	}

	/**
	 * Render item caption (name, rating and notice)
	 * @param Offer $model 
	 * @return string
	 */
	protected function renderCaption($model)
	{
		//name
		$s = $model->name;
		if (!empty($model->model)) {
			$s .= ' ' . $model->model;
		}
		$name = Html::tag('div', Html::a(Html::encode($s), $this->createUrl($model)), ['class'=>'offer-name']);

		//rating
		$rating = '';

		//notice
		$notice = '';
		// $notice = Html::tag('div', Html::a(Html::encode($model->notice), $url), ['offer-notice']);

		return Html::tag('div', $name . $rating . $notice, ['class' => 'offer-caption']);
	}

	/**
	 * Render price and buttons
	 * @param Offer $model 
	 * @return string
	 */
	protected function renderControls($model)
	{
		$formatter = Yii::$app->getFormatter();
		$currency = PriceHelper::getCurrency($model->currency_id);

		//old price
		$s = '';
		if (!empty($model->oldPrice))
			$s = PriceHelper::render('s', $model->oldPrice, $currency);
		$oldPrice = Html::tag('div', $s, ['class' => 'offer-old-price']);

		//price
		$s = PriceHelper::render('span', $model->price, $currency);
		$price = Html::tag('div', $s, ['class' => 'offer-price']);


		//buttons
		$buttons = $available = '';

		return Html::tag('div', $oldPrice . $price . $buttons . $available, ['class' => 'offer-controls']);
	}

}
