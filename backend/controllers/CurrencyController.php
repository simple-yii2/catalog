<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

use cms\catalog\backend\models\CurrencySearch;
use cms\catalog\backend\models\CurrencyForm;
use cms\catalog\common\models\Currency;

class CurrencyController extends Controller
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					['allow' => true, 'roles' => ['Catalog']],
				],
			],
		];
	}

	/**
	 * List
	 * @return string
	 */
	public function actionIndex()
	{
		$search = new CurrencySearch;

		return $this->render('index', [
			'search' => $search,
		]);
	}

	/**
	 * Create
	 * @return string
	 */
	public function actionCreate()
	{
		$model = new CurrencyForm;

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));

			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Update
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$object = Currency::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$model = new CurrencyForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));

			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Delete
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$object = Currency::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		if ($object->delete())
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Item deleted successfully.'));

		return $this->redirect(['index']);
	}

}
