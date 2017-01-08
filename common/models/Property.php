<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;

use helpers\Translit;

class Property extends ActiveRecord
{

	const BOOLEAN = 0;
	const INTEGER = 1;
	const FLOAT = 2;
	const SELECT = 3;

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
		self::FLOAT => 'Decimal',
		self::SELECT => 'Select',
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
		return [self::SELECT];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogProperty';
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

	/**
	 * Making page alias from title and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->title);
	}

	public function validateValue($value)
	{
		switch ($this->type) {
			case self::BOOLEAN:
				return $this->validateBoolean($value);
				break;

			case self::INTEGER:
				return $this->validateInteger($value);
				break;

			case self::FLOAT:
				return $this->validateFloat($value);
				break;

			case self::SELECT:
				return $this->validateSelect($value);
				break;
		}
	}

	private function validateBoolean($value)
	{
		return $value == '0' || $value == '1';
	}

	private function validateInteger($value)
	{
		return preg_match('/^\s*[+-]?\d+\s*$/', "$value");
	}

	private function validateFloat($value)
	{
		return preg_match('/^\s*[+-]?\d+(?:\.\d+)?\s*$/', "$value");
	}

	private function validateSelect($value)
	{
		return in_array($value, $this->getValues());
	}

	public function formatValue($value)
	{
		switch ($this->type) {
			case self::BOOLEAN:
				return $this->formatBoolean($value);
				break;

			case self::INTEGER:
				return $this->formatInteger($value);
				break;

			case self::FLOAT:
				return $this->formatFloat($value);
				break;

			case self::SELECT:
				return $this->formatSelect($value);
				break;
		}
	}

	private function formatInteger($value)
	{
		return (integer) trim($value);
	}

	private function formatFloat($value)
	{
		return (float) trim($value);
	}

}
