<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Store;

/**
 * Search model
 */
class StoreSearch extends Store {

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'type' => Yii::t('catalog', 'Type'),
			'name' => Yii::t('catalog', 'Name'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['type', 'in', 'range' => self::getTypes()],
			['name', 'string'],
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
		$query->andFilterWhere(['=', 'type', $this->type]);
		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}

}
