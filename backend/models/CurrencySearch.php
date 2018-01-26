<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Currency;

/**
 * Search model
 */
class CurrencySearch extends Currency {

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => Yii::t('catalog', 'Name'),
			'code' => Yii::t('catalog', 'Code'),
			'rate' => Yii::t('catalog', 'Rate'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['code', 'string'],
		];
	}

	/**
	 * Search function
	 * @param array|null $params Attributes array
	 * @return ActiveDataProvider
	 */
	public function getDataProvider($params = null) {
		if ($params === null)
			$params = Yii::$app->getRequest()->get();

		//ActiveQuery
		$query = static::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		//return data provider if no search
		if (!($this->load($params) && $this->validate()))
			return $dataProvider;

		//search
		$query->andFilterWhere(['like', 'code', $this->code]);

		return $dataProvider;
	}

}
