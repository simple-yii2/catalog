<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;

class Store extends ActiveRecord
{

	//types
	const TYPE_SALES = 0;
	const TYPE_PICKUP = 1;
	const TYPE_SALES_PICKUP = 2;
	const TYPE_STORE = 3;

	/**
	 * @var string[] Type names
	 */
	private static $typeNames = [
		self::TYPE_SALES => 'Sales area',
		self::TYPE_PICKUP => 'Pickup point',
		self::TYPE_SALES_PICKUP => 'Sales area and pickup point',
		self::TYPE_STORE => 'Store',
	];

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'catalog_store';
	}

	/**
	 * Types
	 * @return integer[]
	 */
	public static function getTypes()
	{
		return array_keys(self::$typeNames);
	}

	/**
	 * Type names
	 * @return string[]
	 */
	public static function getTypeNames()
	{
		return array_map(function($v) {
			return Yii::t('catalog', $v);
		}, self::$typeNames);
	}

}
