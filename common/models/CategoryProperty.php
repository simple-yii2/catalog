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

	/**
	 * @var boolean properties from parent categories is read-only
	 */
	public $readOnly = false;

	/**
	 * @var string[] type names
	 */
	private static $typeNames = [
		self::BOOLEAN => 'Boolean',
		self::INTEGER => 'Integer',
		self::FLOAT => 'Fractional',
		self::SELECT => 'Select',
		self::MULTIPLE => 'Multiple select',
	];

	/**
	 * Getter for type names with translation
	 * @return string[]
	 */	
	public static function getTypeNames()
	{
		return array_map(function($name) {
			return Yii::t('catalog', $name);
		}, self::$typeNames);
	}

	/**
	 * Getter for types which values needed
	 * @return integer[]
	 */
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
