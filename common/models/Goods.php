<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

use helpers\Translit;

class Goods extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogGoods';
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
	 * Category relation
	 * @return ActiveQuery;
	 */
	public function getCategory()
	{
		return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}

	/**
	 * Properties relation
	 * @return ActiveQuery
	 */
	public function getProperties()
	{
		return $this->hasMany(GoodsProperty::className(), ['goods_id' => 'id']);
	}

	/**
	 * Find by alias
	 * @param sring $alias alias or id
	 * @return static
	 */
	public static function findByAlias($alias)
	{
		$model = static::findOne(['alias' => $alias]);
		if ($model === null)
			$model = static::findOne(['id' => $alias]);

		return $model;
	}

	/**
	 * Making alias from title and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->title . '-' . $this->id);
	}

}
