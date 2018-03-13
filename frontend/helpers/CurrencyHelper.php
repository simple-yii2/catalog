<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Settings;

/**
 * Currency helper
 */
class CurrencyHelper {

	/**
	 * @var integer currency model id
	 */
	private static $_currency_id;

	/**
	 * @var Currency currency model
	 */
	private static $_currency;

	/**
	 * Currency id getter
	 * @return integer
	 */
	public static function getCurrency_id()
	{
		if (self::$_currency_id !== null) {
			return self::$_currency_id;
		}

		//try to get it from cookies
		$cookies = Yii::$app->getRequest()->getCookies();
		$id = $cookies->getValue('currency_id');
		if ($id !== null) {
			return self::$_currency_id = $id;
		}

		//try to get it from settings
		$settings = Settings::find()->one();
		if ($settings !== null) {
			return self::$_currency_id = $settings->defaultCurrency_id;
		}

		return null;
	}

	/**
	 * Currency getter
	 * @return Currency
	 */
	public static function getCurrency()
	{
		if (self::$_currency !== null) {
			return self::$_currency;
		}

		return self::$_currency = Currency::findOne(self::getCurrency_id());
	}

	/**
	 * Currency setter
	 * @param Currency $value 
	 * @return void
	 */
	public function setCurrency(Currency $value)
	{
		self::$_currency = $value;

		$cookies = Yii::$app->getResponse()->getCookies();
		$cookies->add(new \yii\web\Cookie([
			'name' => 'currency_id',
			'value' => $value->id,
		]));
	}

}
