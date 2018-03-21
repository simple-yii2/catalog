<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Product;

/**
 * Search model
 */
class ProductSearch extends Product {

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'category_id' => Yii::t('catalog', 'Category'),
			'name' => Yii::t('catalog', 'Name'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['category_id', 'integer'],
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
		$query = static::find()->orderBy(['modifyDate' => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		//return data provider if no search
		if (!($this->load($params) && $this->validate()))
			return $dataProvider;

		//search
		$query->andFilterWhere(['category_id' => $this->category_id]);
		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}

}
