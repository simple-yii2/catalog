<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\widgets\assets\OfferItemAsset;

class OfferItem extends Widget
{

	public $model;

	public $options = ['class' => 'offer-item'];

	protected $_url;

	public function init()
	{
		parent::init();
		OfferItemAsset::register($this->getView());
	}

	public function run()
	{
		$model = $this->model;

		$thumb = $this->renderThumb($model);
		$caption = $this->renderCaption($model);
		$controls = $this->renderControls($model);

		echo Html::tag('div', $thumb . $caption . $controls, ['class' => $this->options]);
	}

	protected function createUrl($model)
	{
		if ($this->_url !== null)
			return $this->_url;

		return $this->_url = CatalogHelper::createOfferUrl($model);
	}

	protected function renderThumb($model)
	{
		//image
		$image = '';
		if (!empty($model->thumb))
			$image = Html::a(Html::img($model->thumb), $this->createUrl($model));

		return Html::tag('div', $image, ['class' => 'offer-thumb']);
	}

	protected function renderCaption($model)
	{
		//name
		$name = Html::tag('div', Html::a(Html::encode($model->name), $this->createUrl($model)), ['class'=>'offer-name']);

		//rating
		$rating = '';

		//notice
		$notice = '';
		// $notice = Html::tag('div', Html::a(Html::encode($model->notice), $url), ['offer-notice']);

		return Html::tag('div', $name . $rating . $notice, ['class' => 'offer-caption']);
	}

	protected function renderControls($model)
	{
		$formatter = Yii::$app->getFormatter();

		//old price
		$s = '';
		if (!empty($model->oldPrice))
			$s = Html::tag('s', $formatter->asDecimal($model->oldPrice));
		$oldPrice = Html::tag('div', $s, ['class' => 'offer-old-price']);

		//price
		$s = Html::tag('span', $formatter->asDecimal($model->price));
		$price = Html::tag('div', $s, ['class' => 'offer-price']);

		//buttons
		$buttons = $available = '';

		return Html::tag('div', $oldPrice . $price . $buttons . $available, ['class' => 'offer-controls']);
	}

}
