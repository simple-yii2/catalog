<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class GoodsProperty extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogGoodsProperty';
	}

}
