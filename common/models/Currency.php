<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

class Currency extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogCurrency';
	}

	/**
	 * @inheritdoc
	 */
	public function __construct($config = [])
	{
		parent::init(array_replace([
			'precision' => -2,
		], $config));
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes)
	{
		//offer price value
		if (array_key_exists('rate', $changedAttributes))
			Offer::updateAll(['priceValue' => new Expression('price * '. $this->rate)], ['currency_id' => $this->id]);
	}

}
