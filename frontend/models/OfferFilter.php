<?php

namespace cms\catalog\frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\CategoryProperty;
use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferProperty;
use cms\catalog\common\models\Settings;
use cms\catalog\common\models\Vendor;
use cms\catalog\frontend\helpers\CurrencyHelper;
use cms\catalog\frontend\helpers\FilterHelper;

class OfferFilter extends Model
{

	/**
	 * @var Category
	 */
	public $category;

	/**
	 * @var string
	 */
	public $price;

	/**
	 * @var string
	 */
	public $vendor;

	/**
	 * @var ActiveQuery
	 */
	private $_query;

	/**
	 * @var CategoryProperty[]
	 */
	private $_properties;

	/**
	 * @var integer
	 */
	private $_propertyCount;

	/**
	 * @var float[]
	 */
	private $_rates;

	/**
	 * @var integer
	 */
	private $_defaultCurrency_id;

	/**
	 * @var integer[]
	 */
	private $_vendor_ids;

	/**
	 * @var [float, float]
	 */
	private $_priceRange;

	/**
	 * @var array
	 */
	private $_vendorItems;

	/**
	 * Active query getter
	 * @return ActiveQuery
	 */
	public function getQuery()
	{
		if ($this->_query !== null)
			return $this->_query;

		//make new instance of active query
		$query = Offer::find()->alias('t')->groupBy(['t.id']);

		//apply conditions
		$this->applyCategoryCondition($query);
		$this->applyPriceCondition($query);
		$this->applyVendorCondition($query);
		$this->applyPropertiesCondition($query);

		return $this->_query = $query;
	}

	/**
	 * Category condition applyer
	 * @param ActiveQuery $query 
	 * @return void
	 */
	private function applyCategoryCondition($query)
	{
		if ($this->category === null)
			return;

		$query->andWhere(['and',
			['>=', 'category_lft', $this->category->lft],
			['<=', 'category_rgt', $this->category->rgt],
		]);
	}

	/**
	 * Price condition applyer
	 * @param ActiveQuery $query 
	 * @return void
	 */
	private function applyPriceCondition($query)
	{
		if (empty($this->price))
			return;

		list($from, $to) = FilterHelper::rangeItems($this->price);
		//current rate
		$currency = CurrencyHelper::getCurrency();
		$current_rate = $currency === null ? 1 : $currency->rate;

		//from
		if ($from !== null)
			$query->andWhere(['>=', 'priceValue', $from * $current_rate]);

		//to
		if ($to !== null)
			$query->andWhere(['<=', 'priceValue', $to * $current_rate]);
	}

	/**
	 * Vendor condition applyer
	 * @param ActiveQuery $query 
	 * @return void
	 */
	private function applyVendorCondition($query)
	{
		if (empty($this->vendor))
			return;

		$query->andWhere(['in', 'vendor_id', $this->getVendor_ids()]);
	}

	/**
	 * Properties condition applyer
	 * @param ActiveQuery $query 
	 * @return void
	 */
	private function applyPropertiesCondition($query)
	{
		foreach ($this->getProperties() as $property) {
			$value = $this->getPropertyValue($property);
			if ($value != '') {
				$alias = 'p' . $property->id;
				$query->leftJoin(OfferProperty::tableName() . " {$alias}", "{$alias}.offer_id = t.id AND {$alias}.property_id = " . $property->id);
				switch ($property->type) {
					case CategoryProperty::TYPE_INTEGER:
					case CategoryProperty::TYPE_FLOAT:
						list($from, $to) = FilterHelper::rangeItems($value);
						if ($from !== null)
							$query->andWhere(['>=', "{$alias}.value", $from]);
						if ($to !== null)
							$query->andWhere(['<=', "{$alias}.value", $to]);
						break;
					case CategoryProperty::TYPE_BOOLEAN:
					case CategoryProperty::TYPE_SELECT:
						$query->andWhere(['in', "{$alias}.value", FilterHelper::selectItems($value)]);
						break;
				}
			}
		}
	}

	/**
	 * Properties of current category getter
	 * @return CategoryProperty[]
	 */
	public function getProperties()
	{
		if ($this->_properties !== null)
			return $this->_properties;

		$category = $this->category;
		if ($category === null)
			return [];

		return $this->_properties = array_merge($category->getParentProperties(), $category->properties);
	}

	/**
	 * Count of properties
	 * @return integer;
	 */
	public function getPropertyCount()
	{
		if ($this->_propertyCount !== null) {
			return $this->_propertyCount;
		}

		$count = sizeof($this->getProperties());

		//price
		list($min, $max) = $this->getPriceRange();
		if ($min < $max) {
			$count++;
		}

		//vendor
		$items = $this->getVendorItems();
		if (!empty($items)) {
			$count++;
		}

		return $this->_propertyCount = $count;
	}

	/**
	 * Checked vendors ids getter
	 * @return integer[]
	 */
	public function getVendor_ids()
	{
		if ($this->_vendor_ids !== null)
			return $this->_vendor_ids;

		$alias = FilterHelper::selectItems($this->vendor);
		$ids = [];
		foreach (Vendor::find()->select(['id'])->andWhere(['in', 'alias', $alias])->asArray()->all() as $row)
			$ids[] = $row['id'];

		return $this->_vendor_ids = $ids;
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->setAttributes(Yii::$app->getRequest()->get(), false);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'price' => Yii::t('catalog', 'Price'),
			'vendor' => Yii::t('catalog', 'Vendor'),
		];
	}

	/**
	 * Data provider getter
	 * @return ActiveDataProvider
	 */
	public function getDataProvider($config = [])
	{
		$query = $this->getQuery();

		return new ActiveDataProvider(array_replace_recursive([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 24,
				'pageParam' => 'p',
			],
		], $config));
	}

	/**
	 * Returns min and max price values for query
	 * @return [float, float]
	 */
	public function getPriceRange()
	{
		if ($this->_priceRange !== null) {
			return $this->_priceRange;
		}

		//rates
		$currency = CurrencyHelper::getCurrency();

		//default
		$min = $max = null;

		if ($currency !== null) {
			$query = clone $this->getQuery();
			$query->groupBy = [];

			$row = $query->select(['MIN(priceValue) AS min', 'MAX(priceValue) AS max'])->asArray()->one();

			$min = round($row['min'] * $currency->rate, $currency->precision);
			$max = round($row['max'] * $currency->rate, $currency->precision);
		}

		return $this->_priceRange = [$min, $max];
	}

	/**
	 * Vendor items getter for drop down list etc.
	 * @return array
	 */
	public function getVendorItems()
	{
		if ($this->_vendorItems !== null) {
			return $this->_vendorItems;
		}

		$query = clone $this->getQuery();
		foreach ($query->where as $key => $value) {
			if (is_array($value) && ArrayHelper::getValue($value, 1) == 'vendor_id')
				unset($query->where[$key]);
		}

		$query->select(['vendor_id', 'COUNT(*) AS cnt'])->groupBy(['vendor_id'])->asArray();

		$rows = [];
		foreach ($query->all() as $row) {
			if ($row['vendor_id'] !== null)
				$rows[$row['vendor_id']] = ['count' => $row['cnt']];
		}

		$items = [];
		if (!empty($rows)) {
			foreach (Vendor::find()->where(['id' => array_keys($rows)])->orderBy(['name' => SORT_ASC])->all() as $object) {
				$items[] = array_merge($rows[$object->id], [
					'title' => $object->name,
					'value' => $object->alias,
				]);
			}
		}

		return $this->_vendorItems = $items;
	}

	/**
	 * "Select" type property items getter for drop down list etc.
	 * @param CategoryProperty $property 
	 * @return array
	 */
	public function getPropertyItems($property)
	{
		$query = clone $this->getQuery();
		foreach ($query->where as $key => $value) {
			$field = 'p' . $property->id . '.value';
			if (is_array($value) && ArrayHelper::getValue($value, 1) == $field)
				unset($query->where[$key]);
		}

		$query->select(['p.value', 'COUNT(*) AS cnt'])->leftJoin(OfferProperty::tableName() . ' p', 'p.offer_id = t.id')->andWhere(['p.property_id' => $property->id])->groupBy(['p.value'])->asArray();

		$items = [];
		foreach ($query->all() as $row)
			$items[] = ['title' => $row['value'], 'count' => $row['cnt']];

		return $items;
	}

	/**
	 * Return property value from request
	 * @param CategoryProperty $property 
	 * @return string
	 */
	public function getPropertyValue($property)
	{
		return Yii::$app->getRequest()->get($property->alias, '');
	}

	public function getPropertyRange($property)
	{
		$query = clone $this->getQuery();
		foreach ($query->where as $key => $value) {
			$field = 'p' . $property->id . '.value';
			if (is_array($value) && ArrayHelper::getValue($value, 1) == $field)
				unset($query->where[$key]);
		}

		$query->select(['MIN(p.value) AS min_value', 'MAX(p.value) AS max_value'])->leftJoin(OfferProperty::tableName() . ' p', 'p.offer_id = t.id')->andWhere(['p.property_id' => $property->id])->groupBy(['p.property_id'])->asArray();

		$row = $query->one();

		return [(float) $row['min_value'], (float) $row['max_value']];
	}

}
