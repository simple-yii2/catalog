<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;

use helpers\Translit;

class CategoryProperty extends ActiveRecord
{

	const TYPE_BOOLEAN = 0;
	const TYPE_INTEGER = 1;
	const TYPE_FLOAT = 2;
	const TYPE_SELECT = 3;

	/**
	 * @var boolean properties from parent categories is read-only
	 */
	public $readOnly = false;

	/**
	 * @var string[] type names
	 */
	private static $typeNames = [
		self::TYPE_BOOLEAN => 'Logical',
		self::TYPE_INTEGER => 'Integer',
		self::TYPE_FLOAT => 'Decimal',
		self::TYPE_SELECT => 'Select',
	];

	/**
	 * Getter for types
	 * @return integer[]
	 */
	public static function getTypes()
	{
		return array_keys(self::$typeNames);
	}

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
		return [self::TYPE_SELECT];
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
	 * @return string[]
	 */
	public function getValues()
	{
		$result = @unserialize($this->svalues);
		
		if (!is_array($result))
			$result = [];

		return $result;
	}

	/**
	 * Values setter
	 * @param string[] $value 
	 * @return void
	 */
	public function setValues($value)
	{
		$this->svalues = serialize($value);
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->type = self::TYPE_INTEGER;
	}

	/**
	 * Making page alias from name
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->name);
	}

	/**
	 * Value validation
	 * @param string $value 
	 * @return boolean
	 */
	public function validateValue($value)
	{
		switch ($this->type) {
			case self::TYPE_BOOLEAN:
				return $this->validateBoolean($value);
				break;

			case self::TYPE_INTEGER:
				return $this->validateInteger($value);
				break;

			case self::TYPE_FLOAT:
				return $this->validateFloat($value);
				break;

			case self::TYPE_SELECT:
				return $this->validateSelect($value);
				break;
		}
	}

	/**
	 * Boolean validation
	 * @param string $value 
	 * @return boolean
	 */
	private function validateBoolean($value)
	{
		return $value == '0' || $value == '1';
	}

	/**
	 * Integer validation
	 * @param string $value 
	 * @return boolean
	 */
	private function validateInteger($value)
	{
		return preg_match('/^\s*[+-]?\d+\s*$/', "$value");
	}

	/**
	 * Decimal validation
	 * @param string $value 
	 * @return boolean
	 */
	private function validateFloat($value)
	{
		return preg_match('/^\s*[+-]?\d+(?:\.\d+)?\s*$/', "$value");
	}

	/**
	 * Select validation
	 * @param string $value 
	 * @return boolean
	 */
	private function validateSelect($value)
	{
		return in_array($value, $this->getValues());
	}

	/**
	 * Value formatting for save
	 * @param string $value 
	 * @return string
	 */
	public function formatValue($value)
	{
		switch ($this->type) {
			case self::TYPE_BOOLEAN:
				return $this->formatBoolean($value);
				break;

			case self::TYPE_INTEGER:
				return $this->formatInteger($value);
				break;

			case self::TYPE_FLOAT:
				return $this->formatFloat($value);
				break;

			case self::TYPE_SELECT:
				return $this->formatSelect($value);
				break;
		}
	}

	/**
	 * Boolean formatting
	 * @param mixed $value 
	 * @return string
	 */
	private function formatBoolean($value)
	{
		if ($value === null || $value === '')
			return null;

		return $value == 0 ? '0' : '1';
	}

	/**
	 * Integer formatting
	 * @param mixed $value 
	 * @return string
	 */
	private function formatInteger($value)
	{
		$value = (integer) trim($value);

		return (string) $value;
	}

	/**
	 * Decimal formatting
	 * @param mixed $value 
	 * @return string
	 */
	private function formatFloat($value)
	{
		$value = (float) trim($value);

		return (string) $value;
	}

	/**
	 * Select formatting
	 * @param mixed $value 
	 * @return string
	 */
	private function formatSelect($value)
	{
		return $value;
	}

}
