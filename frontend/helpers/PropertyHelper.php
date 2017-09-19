<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\CategoryProperty;
use cms\catalog\common\models\OfferProperty;

class PropertyHelper
{

	/**
	 * Render property value
	 * @param OfferProperty $object 
	 * @return string
	 */
	public static function renderValue(OfferProperty $object)
	{
		$categoryProperty = $object->categoryProperty;
		if ($categoryProperty === null)
			return null;

		$unit = '';
		if (!empty($categoryProperty->unit))
			$unit .= ' ' . $categoryProperty->unit;


		switch ($categoryProperty->type) {
			case CategoryProperty::TYPE_BOOLEAN:
				return self::renderBoolean($object->value) . $unit;
				break;

			case CategoryProperty::TYPE_INTEGER:
				return self::renderInteger($object->value) . $unit;
				break;

			case CategoryProperty::TYPE_FLOAT:
				return self::renderFloat($object->value) . $unit;
				break;

			case CategoryProperty::TYPE_SELECT:
				return self::renderSelect($object->value) . $unit;
				break;
		}
	}

	/**
	 * Render value as boolean
	 * @param string $value 
	 * @return string
	 */
	private static function renderBoolean($value)
	{
		return Yii::$app->formatter->asBoolean($value == 1);
	}

	/**
	 * Render value as float
	 * @param string $value 
	 * @return string
	 */
	private static function renderFloat($value)
	{
		return Yii::$app->formatter->asDecimal($value);
	}

	/**
	 * Render value as select
	 * @param string $value 
	 * @return string
	 */
	private static function renderSelect($value)
	{
		return $value;
	}

}
