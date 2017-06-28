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

}
