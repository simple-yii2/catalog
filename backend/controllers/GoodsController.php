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
		$object = new Goods;

		$model = new GoodsForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			$object->category->updateGoodsCount();

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

		$category = $object->category;

		$model = new GoodsForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			$object->category->updateGoodsCount();

			if ($category->id != $object->category->id)
				$category->updateGoodsCount();

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

		$category = $object->category;

		foreach ($object->images as $item) {
			Yii::$app->storage->removeObject($item);
			$item->delete();
		}

		foreach ($object->properties as $item) {
			$item->delete();
		}

		if ($object->delete()) {
			$category->updateGoodsCount();

			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Item deleted successfully.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Properties update needed when category is changed
	 * @param integer|null $id
	 * @return string
	 */
	public function actionProperties($id = null)
	{
		$model = new GoodsForm(Goods::findOne($id));

		$model->load(Yii::$app->getRequest()->post());

		return $this->renderAjax('form', [
			'model' => $model,
		]);
	}

}
