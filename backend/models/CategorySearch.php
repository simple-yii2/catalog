<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Category;

/**
 * Search model
 */
class CategorySearch extends Category {

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'title' => Yii::t('catalog', 'Title'),
		];
	}

	/**
	 * Search function
	 * @return ActiveDataProvider
	 */
	public function getDataProvider() {
		return new ActiveDataProvider([
			'query' => static::find(),
		]);
	}

}
