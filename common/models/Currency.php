<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class Currency extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogCurrency';
	}

}
