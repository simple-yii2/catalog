<?php

namespace cms\catalog\frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\CategoryProperty;
use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferProperty;
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
		//category
		if ($this->category !== null) {
			$query->andWhere(['and',
				['>=', 'category_lft', $this->category->lft],
				['<=', 'category_rgt', $this->category->rgt],
			]);
		}
		//price
		if (!empty($this->price)) {
			list($from, $to) = FilterHelper::rangeItems($this->price);
			if ($from !== null)
				$query->andWhere(['>=', 'price', $from]);

			if ($to !== null)
				$query->andWhere(['<=', 'price', $to]);
		}
		//vendor
		if (!empty($this->vendor)) {
			$query->andWhere(['in', 'vendor_id', FilterHelper::selectItems($this->vendor)]);
		}
		//properties
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

		return $this->_query = $query;
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
		$query = clone $this->getQuery();
		return [(float) $query->min('price'), (float) $query->max('price')];
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
