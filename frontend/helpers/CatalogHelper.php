<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Offer;
use cms\catalog\frontend\Module;

/**
 * Frontend module helper
 */
class CatalogHelper
{

	/**
	 * @var string Route to catalog module
	 */
	private static $_moduleName;

	/**
	 * Determine catalog module name
	 * @return string|null
	 */
	private static function getModuleName()
	{
		if (self::$_moduleName !== null)
			return self::$_moduleName;

		$moduleClass = Module::className();
		foreach (Yii::$app->getModules() as $name => $module) {
			if (is_string($module)) {
				$class = $module;
			} elseif (is_array($module)) {
				$class = $module['class'];
			} else {
				$class = get_class($module);
			}

			if ($class == $moduleClass)
				return self::$_moduleName = $name;
		}

		return null;
	}

	/**
	 * Create url to category offers
	 * @param Category $object 
	 * @return string|array
	 */
	public static function createCategoryUrl(Category $object)
	{
		$name = self::getModuleName();
		if ($name === null)
			return '#';

		return ["/{$name}/offer/index", 'alias' => $object->alias];
	}

	/**
	 * Create url to offer view
	 * @param Offer $object 
	 * @return string|array
	 */
	public static function createOfferUrl(Offer $object)
	{
		$name = self::getModuleName();
		if ($name === null)
			return '#';

		return ["/{$name}/offer/view", 'alias' => $object->alias];
	}

	/**
	 * Make menu items for given category, if null given, use root
	 * @param Category|null $category 
	 * @return array
	 */
	public static function getMenuItems(Category $category = null)
	{
		if ($category === null)
			$category = Category::find()->roots()->one();

		if ($category === null)
			return [];

		$objects = array_merge([$category], $category->children()->all());

		$i = 0;
		$offerCount = 0;
		$item = self::makeBranch($objects, $i, $offerCount);

		return ArrayHelper::getValue($item, 'items', []);
	}

	/**
	 * Make branch for menu
	 * @param Category[] $objects 
	 * @param integer &$i 
	 * @param integer &$offerCount 
	 * @return array
	 */
	private static function makeBranch($objects, &$i, &$offerCount = 0)
	{
		$object = $objects[$i];
		$offerCount += $object->offerCount;

		$result = [
			'label' => Html::encode($object->title),
			'url' => self::createCategoryUrl($object),
		];

		$items = [];
		while (($i < sizeof($objects) - 1) && $objects[$i + 1]->depth > $object->depth) {
			$i++;
			$o = $objects[$i];

			$c = 0;
			$item = self::makeBranch($objects, $i, $c);
			$offerCount += $c;

			if ($c)
				$items[] = $item;
		}

		if ($offerCount > 0)
			$result['label'] .= ' ' . Html::tag('span', $offerCount, ['count']);

		if (!empty($items))
			$result['items'] = $items;

		return $result;
	}

}
