<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Product;
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
	 * Create url to category products
	 * @param Category $object 
	 * @return string|array
	 */
	public static function createCategoryUrl(Category $object)
	{
		$name = self::getModuleName();
		if ($name === null)
			return '#';

		return ["/{$name}/product/index", 'alias' => $object->alias];
	}

	/**
	 * Create url to product view
	 * @param Product $object 
	 * @return string|array
	 */
	public static function createProductUrl(Product $object)
	{
		$name = self::getModuleName();
		if ($name === null)
			return '#';

		return ["/{$name}/product/view", 'alias' => $object->alias];
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
		$productCount = 0;
		$item = self::makeBranch($objects, $i, $productCount);

		return ArrayHelper::getValue($item, 'items', []);
	}

	/**
	 * Make branch for menu
	 * @param Category[] $objects 
	 * @param integer &$i 
	 * @param integer &$productCount 
	 * @return array
	 */
	private static function makeBranch($objects, &$i, &$productCount = 0)
	{
		$object = $objects[$i];
		$productCount += $object->productCount;

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
			$productCount += $c;

			if ($c)
				$items[] = $item;
		}

		if ($productCount > 0)
			$result['label'] .= ' ' . Html::tag('span', $productCount, ['count']);

		if (!empty($items))
			$result['items'] = $items;

		return $result;
	}

}
