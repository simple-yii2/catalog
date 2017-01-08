<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Goods;

/**
 * Search model
 */
class GoodsSearch extends Goods {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['title', 'string'],
		];
	}

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
	 * @param array $params Attributes array
	 * @return yii\data\ActiveDataProvider
	 */
	public function search($params) {
		//ActiveQuery
		$query = static::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		//return data provider if no search
		if (!($this->load($params) && $this->validate()))
			return $dataProvider;

		//search
		$query->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}

}
