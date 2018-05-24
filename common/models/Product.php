<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use helpers\Translit;

class Product extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'catalog_product';
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert) === false)
			return false;

		//price value
		if ($this->isAttributeChanged('price') || $this->isAttributeChanged('currency_id')) {
			$value = $this->price;
			if ($this->currency !== null)
				$value *= $this->currency->rate;

			$this->priceValue = $value;
		}

		return true;
	}

	/**
	 * Category relation
	 * @return yii\db\ActiveQueryInterface;
	 */
	public function getCategory()
	{
		return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}

	/**
	 * Currency relation
	 * @return yii\db\ActiveQueryInterface;
	 */
	public function getCurrency()
	{
		return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
	}

	/**
	 * Barcodes relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getBarcodes()
	{
		return $this->hasMany(ProductBarcode::className(), ['product_id' => 'id']);
	}

	/**
	 * Properties relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getProperties()
	{
		return $this->hasMany(ProductProperty::className(), ['product_id' => 'id']);
	}

	/**
	 * Images relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getImages()
	{
		return $this->hasMany(ProductImage::className(), ['product_id' => 'id']);
	}

	/**
	 * Recommended relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getRecommended()
	{
		return $this->hasMany(Product::className(), ['id' => 'recommended_id'])->viaTable('catalog_product_recommended', ['product_id' => 'id']);
	}

	/**
	 * Stores quantity relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getStores()
	{
		return $this->hasMany(StoreProduct::className(), ['product_id' => 'id']);
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
	 * Making alias from name and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->name . ' ' . $this->model . '-' . $this->id);
	}

}
