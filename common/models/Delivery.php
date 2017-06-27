<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class Delivery extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogDelivery';
	}

}
