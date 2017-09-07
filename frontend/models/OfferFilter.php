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
	 * @var float[]
	 */
	private $_rates;

	/**
	 * @var integer
	 */
	private $_defaultCurrency_id;

	/**
	 * @var float
	 */
	private $_currentCurrency;

	/**
	 * Active query getter
	 * @return ActiveQuery
	 */
	private function getQuery()
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
		//currencies rates
		$rates = $this->getRates();
		//default currency
		$default_id = $this->getDefaultCurrency_id();
		//current rate
		$currency = $this->getCurrentCurrency();
		$current_rate = $currency === null ? 1 : $currency->rate;

		//from
		if ($from !== null) {
			$conditions = ['or'];
			if ($default_id !== null && array_key_exists($default_id, $rates))
				$conditions[] = ['and',
					['currency_id' => null],
					['>=', 'price', $from * $current_rate / $rates[$default_id]],
				];
			foreach ($rates as $id => $rate) {
				$value = $from * $current_rate / $rate;
				$conditions[] = ['and',
					['=', 'currency_id', $id],
					['>=', 'price', $value],
				];
			}
			$query->andWhere($conditions);
		}

		//to
		if ($to !== null) {
			$conditions = ['or'];
			if ($default_id !== null && array_key_exists($default_id, $rates))
				$conditions[] = ['and',
					['currency_id' => null],
					['<=', 'price', $to * $current_rate / $rates[$default_id]],
				];
			foreach ($rates as $id => $rate) {
				$value = $to * $current_rate / $rate;
				$conditions[] = ['and',
					['currency_id' => $id],
					['<=', 'price', $value],
				];
			}
			$query->andWhere($conditions);
		}
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

		$query->andWhere(['in', 'vendor_id', FilterHelper::selectItems($this->vendor)]);
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
	 * Currencies rates getter
	 * @return float[]
	 */
	public function getRates()
	{
		if ($this->_rates !== null)
			return $this->_rates;

		$rates = [];
		foreach (Currency::find()->select(['id', 'rate'])->asArray()->all() as $row)
			$rates[$row['id']] = $row['rate'];

		return $this->_rates = $rates;
	}

	/**
	 * Default currency id getter
	 * @return integer
	 */
	public function getDefaultCurrency_id()
	{
		if ($this->_defaultCurrency_id !== null)
			return $this->_defaultCurrency_id;

		$settings = Settings::find()->one();
		return $this->_defaultCurrency_id = $settings === null ? null : $settings['defaultCurrency_id'];
	}

	/**
	 * Current currency getter
	 * @return float
	 */
	public function getCurrentCurrency()
	{
		if ($this->_currentCurrency !== null)
			return $this->_currentCurrency;

		$currency = null;
		if (Yii::$app->has('currency')) 
			$currency = Yii::$app->currency->currency;

		return $this->_currentCurrency = $currency;
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
	public function getDataProvider()
	{
		$query = $this->getQuery();

		return new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 24,
				'pageParam' => 'p',
			],
		]);
	}

	/**
	 * Returns min and max price values for query
	 * @return [float, float]
	 */
	public function getPriceRange()
	{
		//rates
		$rates = $this->getRates();
		$defaultCurrency_id = $this->getDefaultCurrency_id();
		$currency = $this->getCurrentCurrency();

		$aMin = $aMax = [];

		if ($currency !== null) {
			$query = clone $this->getQuery();
			$query->select(['currency_id', 'MIN(price) AS min', 'MAX(price) AS max'])->groupBy(['currency_id'])->asArray();
			foreach ($query->all() as $row) {
				$id = $row['currency_id'];
				if ($id === null)
					$id = $defaultCurrency_id;

				if (!array_key_exists($id, $rates))
					continue;

				$aMin[] = round($row['min'] * $rates[$id] / $currency->rate, $currency->precision);
				$aMax[] = round($row['max'] * $rates[$id] / $currency->rate, $currency->precision);
			}
		}

		return [min($aMin), max($aMax)];
	}

	/**
	 * Vendor items getter for drop down list etc.
	 * @return array
	 */
	public function getVendorItems()
	{
		$query = clone $this->getQuery();
		foreach ($query->where as $key => $value) {
			if (is_array($value) && ArrayHelper::getValue($value, 1) == 'vendor_id')
				unset($query->where[$key]);
		}

		$query->select(['vendor_id', 'COUNT(*) AS cnt'])->groupBy(['vendor_id'])->asArray();

		$rows = [];
		foreach ($query->all() as $row) {
			if ($row['vendor_id'] !== null) {
				$rows[$row['vendor_id']] = [
					'value' => $row['vendor_id'],
					'count' => $row['cnt'],
				];
			}
		}

		$items = [];
		if (!empty($rows)) {
			foreach (Vendor::find()->where(['id' => array_keys($rows)])->orderBy(['name' => SORT_ASC])->all() as $object)
				$items[] = array_merge($rows[$object->id], ['title' => $object->name]);
		}

		return $items;
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
