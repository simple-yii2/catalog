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

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogCategory';
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->active = true;
	}

	/**
	 * Properties relation
	 * @return ActiveQuery
	 */
	public function getProperties()
	{
		return $this->hasMany(CategoryProperty::className(), ['category_id' => 'id']);
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
	 * Making page alias from title and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->title . '-' . $this->id);
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
