<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Settings;

class PriceHelper
{

	/**
	 * @var Currency
	 */
	private static $_currency = false;

	/**
	 * @var Currency[]
	 */
	private static $_currencies;

	/**
	 * Render price
	 * @param string $tag price container tag
	 * @param string $value price
	 * @param Currency|null $currency price currency
	 * @return string
	 */
	public static function render($tag, $value, $currency = null)
	{
		$formatter = Yii::$app->getFormatter();
		$appCurrency = self::getApplicationCurrency();
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

	/**
	 * Get currency by id
	 * @param integer $id 
	 * @return Currency|null
	 */
	public static function getCurrency($id)
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

	/**
	 * Get application currency
	 * @return Currency|null
	 */
	private static function getApplicationCurrency()
	{
		if (self::$_currency !== false)
			return self::$_currency;

		//from component
		$component = Yii::$app->get('currency', false);
		if ($component !== null)
			return self::$_currency = self::getCurrency($component->currency_id);

		//from settings
		$settings = Settings::find()->one();
		if ($settings === null)
			$settings = new Settings;
		return self::$_currency = self::getCurrency($settings->defaultCurrency_id);
	}

}
