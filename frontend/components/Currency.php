<?php

namespace cms\catalog\frontend\components;

use Yii;
use yii\base\Object;
use cms\catalog\common\models;

/**
 * Currency application component
 */
class Currency extends Object {

	/**
	 * @var integer currency model id
	 */
	private $_currency_id;

	/**
	 * @var models\Currency currency model
	 */
	private $_currency;

	/**
	 * Currency id getter
	 * @return integer
	 */
	public function getCurrency_id()
	{
		if ($this->_currency_id !== null)
			return $this->_currency_id;

		//try to get it from cookies
		$cookies = Yii::$app->getRequest()->getCookies();
		$id = $cookies->getValue('currency_id');
		if ($id !== null)
			return $this->_currency_id = $id;

		//try to get it from settings
		$settings = models\Settings::find()->one();
		if ($settings !== null)
			return $this->_currency_id = $settings->defaultCurrency_id;

		return null;
	}

	/**
	 * Currency getter
	 * @return models\Currency
	 */
	public function getCurrency()
	{
		if ($this->_currency !== null)
			return $this->_currency;

		return $this->_currency = models\Currency::findOne($this->getCurrency_id());
	}

	/**
	 * Currency setter
	 * @param models\Currency $value 
	 * @return void
	 */
	public function setCurrency(models\Currency $value)
	{
		$this->_currency = $value;

		$cookies = Yii::$app->getResponse()->getCookies();
		$cookies->add(new \yii\web\Cookie([
			'name' => 'currency_id',
			'value' => $value->id,
		]));
	}

}
