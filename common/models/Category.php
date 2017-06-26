<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use helpers\Translit;
use creocoder\nestedsets\NestedSetsBehavior;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class Category extends ActiveRecord
{

	const PATH_SEPARATOR = ' » ';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogCategory';
	}

	/**
	 * @inheritdoc
	 * Default values
	 */
	public function __construct($config = [])
	{
		parent::__construct(array_merge([
			'active' => true,
			'offerCount' => 0,
		], $config));
	}

	/**
	 * Properties relation
	 * @return ActiveQuery
	 */
	public function getProperties()
	{
		return $this->hasMany(Property::className(), ['category_id' => 'id']);
	}

	/**
	 * Offers relation
	 * @return ActiveQuery
	 */
	public function getOffers()
	{
		return $this->hasMany(Offer::className(), ['category_id' => 'id'])->inverseOf('category');
	}

	/**
	 * Parent properties
	 * @return CategoryProperty[]
	 */
	public function getParentProperties()
	{
		$items = [];
		foreach ($this->parents()->all() as $parent) {
			$items = array_merge($items, $parent->properties);
		}

		array_walk($items, function(&$item) {
			$item->readOnly = true;
		});

		return $items;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'title' => Yii::t('catalog', 'Title'),
		];
	}

	/**
	 * Find by alias
	 * @param sring $alias Alias or id
	 * @return static
	 */
	public static function findByAlias($alias) {
		$model = static::findOne(['alias' => $alias]);
		if ($model === null)
			$model = static::findOne(['id' => $alias]);

		return $model;
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'tree' => [
				'class' => NestedSetsBehavior::className(),
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function find()
	{
		return new CategoryQuery(get_called_class());
	}

	/**
	 * Making alias from title and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->title . '-' . $this->id);
	}

	/**
	 * Making path from title and parent path
	 * @param Category|null $parent 
	 * @return void
	 */
	public function makePath(Category $parent = null)
	{
		if ($parent === null)
			$parent = $this->parents(1)->one();

		$path = '';

		if ($parent !== null)
			$path = $parent->path;

		if (!empty($path))
			$path .= self::PATH_SEPARATOR;

		$this->path = $path . $this->title;
	}

	/**
	 * Update path with children
	 * @param Category|null $parent 
	 * @return void
	 */
	public function updatePath(Category $parent = null)
	{
		$this->makePath($parent);
		$this->update(false, ['path']);

		if ($this->isLeaf())
			return;

		foreach ($this->children(1)->all() as $object)
			$object->updatePath($this);
	}

	/**
	 * Update offer count
	 * @return void
	 */
	public function updateOfferCount()
	{
		$count = $this->getOffers()->count();
		if ($count != $this->offerCount) {
			$this->offerCount = $count;
			$this->update(false, ['offerCount']);
		}
	}

}

class CategoryQuery extends ActiveQuery
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			NestedSetsQueryBehavior::className(),
		];
	}

}
