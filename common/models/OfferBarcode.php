<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class OfferBarcode extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogOfferBarcode';
	}

}
