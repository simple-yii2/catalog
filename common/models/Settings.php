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

	/**
	 * @inheritdoc
	 */
	public function __construct($config = [])
	{
		parent::__construct(array_merge([
			'vendorImageWidth' => 100,
			'vendorImageHeight' => 100,
		], $config));
	}

}
