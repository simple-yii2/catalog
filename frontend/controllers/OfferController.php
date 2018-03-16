<?php

namespace cms\catalog\frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Offer;
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

		if ($model->getQuery()->count() == 0) {
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}

		return $this->render('index', [
			'category' => $category,
			'model' => $model,
		]);
	}

	/**
	 * Offer view
	 * @param string $alias 
	 * @return string
	 */
	public function actionView($alias)
	{
		$model = Offer::findByAlias($alias);
		if ($model === null) {
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

}
