<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;

class CategoryProperty extends ActiveRecord
{

	const BOOLEAN = 0;
	const INTEGER = 1;
	const FLOAT = 2;
	const SELECT = 3;
	const MULTIPLE = 4;

	private static $typeNames = [
		self::BOOLEAN => 'Boolean',
		self::INTEGER => 'Integer',
		self::FLOAT => 'Fractional',
		self::SELECT => 'Select',
		self::MULTIPLE => 'Multiple select',
	];

	public static function getTypeNames()
	{
		return array_map(function($name) {
			return Yii::t('catalog', $name);
		}, self::$typeNames);
	}

	public static function getTypesWithValues()
	{
		return [self::SELECT, self::MULTIPLE];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogCategoryProperty';
	}

	/**
	 * Values getter
	 * @return array
	 */
	public function getValues()
	{
		$result = unserialize($this->values);
		
		if (!is_array($result))
			$result = [];

		return $result;
	}

	/**
	 * Values setter
	 * @param array $value 
	 * @return void
	 */
	public function setValues($value)
	{
		$this->values = serialize($value);
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->type = self::INTEGER;
	}

}
