<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class Settings extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'CatalogSettings';
	}

}
