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
		$form = new CurrencyForm;

		if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));

			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'form' => $form,
		]);
	}

	/**
	 * Update
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$model = Currency::findOne($id);
		if ($model === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$form = new CurrencyForm($model);

		if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));

			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'form' => $form,
		]);
	}

	/**
	 * Delete
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$model = Currency::findOne($id);
		if ($model === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		if ($model->delete())
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Item deleted successfully.'));

		return $this->redirect(['index']);
	}

}
