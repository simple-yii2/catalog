<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

use cms\catalog\backend\models\GoodsForm;
use cms\catalog\backend\models\GoodsSearch;
use cms\catalog\common\models\Goods;

class GoodsController extends Controller
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
		$model = new GoodsSearch;

		return $this->render('index', [
			'dataProvider' => $model->search(Yii::$app->getRequest()->get()),
			'model' => $model,
		]);
	}

	/**
	 * Creating
	 * @return string
	 */
	public function actionCreate()
	{
		$model = new GoodsForm;

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updating
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$object = Goods::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$model = new GoodsForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deleting
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$object = Goods::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		if ($object->delete()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Item deleted successfully.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Properties update needed when category is changed
	 * @param integer $id
	 * @return string
	 */
	public function actionProperties($id)
	{
		$model = new GoodsForm(Goods::findOne($id));

		$model->load(Yii::$app->getRequest()->post());

		return $this->renderAjax('form', [
			'model' => $model,
		]);
	}

}
