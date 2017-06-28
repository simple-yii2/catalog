<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use helpers\Translit;

class Offer extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogOffer';
	}

	/**
	 * @inheritdoc
	 * Default values
	 */
	public function __construct($config = [])
	{
		parent::__construct(array_merge([
			'active' => true,
			'imageCount' => 0,
			'currency_id' => ArrayHelper::getValue(Yii::$app->params, ['catalogSettings', 'defaultCurrency_id']),
		], $config));
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
	 * Barcodes relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getBarcodes()
	{
		return $this->hasMany(OfferBarcode::className(), ['offer_id' => 'id']);
	}

	/**
	 * Delivery relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getDelivery()
	{
		return $this->hasMany(OfferDelivery::className(), ['offer_id' => 'id']);
	}

	/**
	 * Properties relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getProperties()
	{
		return $this->hasMany(OfferProperty::className(), ['offer_id' => 'id']);
	}

	/**
	 * Images relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getImages()
	{
		return $this->hasMany(OfferImage::className(), ['offer_id' => 'id']);
	}

	/**
	 * Recommended relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getRecommended()
	{
		return $this->hasMany(OfferRecommended::className(), ['offer_id' => 'id']);
	}

	/**
	 * Stores quantity relation
	 * @return yii\db\ActiveQueryInterface
	 */
	public function getStores()
	{
		return $this->hasMany(StoreOffer::className(), ['offer_id' => 'id']);
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
		$this->alias = Translit::t($this->name . '-' . $this->id);
	}

}
