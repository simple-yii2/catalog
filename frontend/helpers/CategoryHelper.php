<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Category;
use cms\catalog\frontend\Module;

/**
 * Helper for category in frontend module
 */
class CategoryHelper
{

	/**
	 * @var string Route to catalog module
	 */
	private static $_route;

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
			'url' => self::createUrl($object),
		];

		$items = [];
		while (($i < sizeof($objects) - 1) && $objects[$i + 1]->depth > $object->depth) {
			$i++;
			$o = $objects[$i];

			$c = 0;
			$item = self::makeBranch($objects, $i, $c);
			$offerCount += $c;

			$items[] = $item;
		}

		if ($offerCount > 0)
			$result['label'] .= ' ' . Html::tag('span', $offerCount, ['count']);

		if (!empty($items))
			$result['items'] = $items;

		return $result;
	}

	/**
	 * Create url to category offers
	 * @param Category $category 
	 * @return array
	 */
	private static function createUrl(Category $category)
	{
		if (self::$_route !== null)
			return [self::$_route, 'alias' => $category->alias];

		$route = null;
		$moduleClass = Module::className();
		foreach (Yii::$app->getModules() as $id => $module) {
			if (is_string($module)) {
				$name = $module;
			} elseif (is_array($module)) {
				$name = $module['class'];
			} else {
				$name = get_class($module);
			}

			if ($name == $moduleClass)
				$route = self::$_route = "/{$id}/offer/index";
		}
		if (empty($route))
			return '#';

		return [$route, 'alias' => $category->alias];
	}

}
