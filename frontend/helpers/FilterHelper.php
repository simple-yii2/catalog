<?php

namespace cms\catalog\frontend\helpers;

/**
 * Filter helper
 */
class FilterHelper
{

	/**
	 * Make range value from value items
	 * @param float $from 
	 * @param float $to 
	 * @return string
	 */
	public static function rangeValue($from, $to)
	{
		return $from . '_' . $to;
	}

	/**
	 * Parse range items from value
	 * @param string $value 
	 * @return [from, to]
	 */
	public static function rangeItems($value)
	{
		$a = explode('_', $value);

		$from = $a[0];
		$to = null;

		if (sizeof($a) > 1)
			$to = $a[1];

		if ($from === '')
			$from = null;

		if ($to === '')
			$to = null;

		return [$from, $to];
	}

	public static function selectValue($items)
	{
		$a = array_map(function($v) {
			return str_replace('_', "\e", $v);
		}, $items);

		return implode('_', $a);
	}

	public static function selectItems($value)
	{
		if ($value === '')
			return [];

		$items = explode('_', $value);

		return array_map(function($v) {
			return str_replace("\e", '_', $v);
		}, $items);
	}

}
