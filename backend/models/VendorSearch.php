<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\catalog\common\models\Vendor;

/**
 * Search model
 */
class VendorSearch extends Vendor {

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => Yii::t('catalog', 'Name'),
			'url' => Yii::t('catalog', 'Url'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
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
		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}

}
