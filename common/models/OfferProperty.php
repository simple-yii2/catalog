<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class OfferProperty extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogOfferProperty';
	}

	/**
	 * Category property relation
	 * @return ActiveQueryInterface
	 */
	public function getCategoryProperty()
	{
		return $this->hasOne(CategoryProperty::className(), ['id' => 'property_id']);
	}

}
