<?php

namespace cms\catalog\frontend\controllers;

use yii\web\Controller;

use cms\catalog\common\models\Category;
use cms\catalog\frontend\models\OfferFilter;

class OfferController extends Controller
{

	/**
	 * Offer list
	 * @param string|null $alias 
	 * @return string
	 */
	public function actionIndex($alias = null)
	{
		$category = Category::findByAlias($alias);
		if ($category === null)
			$category = Category::find()->roots()->one();

		$model = new OfferFilter;
		$model->category = $category;

		return $this->render('index', [
			'category' => $category,
			'model' => $model,
		]);
	}

}
