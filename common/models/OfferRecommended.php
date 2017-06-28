<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class OfferRecommended extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogOfferRecommended';
	}

}
