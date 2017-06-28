<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class StoreOffer extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogStoreOffer';
	}

}
