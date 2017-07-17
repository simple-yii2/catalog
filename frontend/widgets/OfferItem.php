<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Settings;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\widgets\assets\OfferItemAsset;

class OfferItem extends Widget
{

	public $model;

	public $options = ['class' => 'offer-item'];

	protected $_url;

	private static $_settings;

	private static $_currency = false;

	private static $_currencies;

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

	protected function getSettings()
	{
		if (self::$_settings !== null)
			return self::$_settings;

		$settings = Settings::find()->one();
		if ($settings === null)
			$settings = new Settings;

		return self::$_settings = $settings;
	}

	protected function getCurrency($id)
	{
		//init currencies if needed
		if (self::$_currencies === null) {
			$items = [];
			foreach (Currency::find()->all() as $item) {
				$items[$item->id] = $item;
			}
			self::$_currencies = $items;
		}

		return ArrayHelper::getValue(self::$_currencies, $id);
	}

	protected function getApplicationCurrency()
	{
		if (self::$_currency !== false)
			return self::$_currency;

		//from component
		$component = Yii::$app->get('currency', false);
		if ($component !== null)
			return self::$_currency = $this->getCurrency($component->currency_id);

		//from settings
		$settings = $this->getSettings();
		return self::$_currency = $this->getCurrency($settings->defaultCurrency_id);
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
		$currency = $this->getCurrency($model->currency_id);

		//old price
		$s = '';
		if (!empty($model->oldPrice))
			$s = $this->renderPrice('s', $model->oldPrice, $currency);
		$oldPrice = Html::tag('div', $s, ['class' => 'offer-old-price']);

		//price
		$s = $this->renderPrice('span', $model->price, $currency);
		$price = Html::tag('div', $s, ['class' => 'offer-price']);


		//buttons
		$buttons = $available = '';

		return Html::tag('div', $oldPrice . $price . $buttons . $available, ['class' => 'offer-controls']);
	}

	protected function renderPrice($tag, $value, $currency = null)
	{
		$formatter = Yii::$app->getFormatter();
		$appCurrency = $this->getApplicationCurrency();
		$c = $appCurrency;
		if ($c === null)
			$c = $currency;			
		$precision = ArrayHelper::getValue($c, 'precision', 0);

		//calc
		if ($appCurrency !== null && $currency !== null && $appCurrency->id != $currency->id)
			$value = $value * $currency->rate / $appCurrency->rate;

		//format
		$r = Html::tag($tag, $formatter->asDecimal($value, $c == null ? 0 : $c->precision));

		//prefix/suffix
		if ($c !== null) {
			if (!empty($c->prefix))
				$r = Html::encode($c->prefix) . '&nbsp;' . $r;
			if (!empty($c->suffix))
				$r .= '&nbsp;' . Html::encode($c->suffix);
		}

		return $r;
	}

}
