<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class OfferDelivery extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogOfferDelivery';
	}

}
