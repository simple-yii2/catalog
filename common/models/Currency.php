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

	/**
	 * @inheritdoc
	 */
	public function __construct($config = [])
	{
		parent::init(array_merge([
			'precision' => -2,
		], $config));
	}

}
