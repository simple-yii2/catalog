<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

use cms\catalog\backend\models\OfferForm;
use cms\catalog\backend\models\OfferSearch;
use cms\catalog\common\models\Offer;
use cms\catalog\common\models\Settings;

class OfferController extends Controller
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
		return $this->render('index', [
			'searchModel' => new OfferSearch,
		]);
	}

	/**
	 * Create
	 * @return string
	 */
	public function actionCreate()
	{
		$this->loadSettings();

		$formModel = new OfferForm;

		if ($formModel->load(Yii::$app->getRequest()->post()) && $formModel->save()) {
			$formModel->getObject()->category->updateOfferCount();

			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'formModel' => $formModel,
		]);
	}

	/**
	 * Update
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$this->loadSettings();

		$object = Offer::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$category = $object->category;

		$formModel = new OfferForm($object);

		if ($formModel->load(Yii::$app->getRequest()->post()) && $formModel->save()) {
			$object->category->updateOfferCount();

			if ($category->id != $object->category->id)
				$category->updateOfferCount();

			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'formModel' => $formModel,
		]);
	}

	/**
	 * Delete
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$object = Offer::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		//barcodes
		foreach ($object->barcodes as $item)
			$item->delete();

		//delivery
		foreach ($object->delivery as $item)
			$item->delete();

		//properties
		foreach ($object->properties as $item)
			$item->delete();

		//images
		foreach ($object->images as $item) {
			Yii::$app->storage->removeObject($item);
			$item->delete();
		}

		//recommended
		foreach ($object->recommended as $item)
			$item->delete();

		//store quantity
		foreach ($object->stores as $item)
			$item->delete();


		//offer
		$category = $object->category;
		if ($object->delete()) {
			$category->updateOfferCount();

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
		$formModel = new OfferForm(Offer::findOne($id));

		$formModel->load(Yii::$app->getRequest()->post());

		return $this->renderAjax('form', [
			'formModel' => $formModel,
		]);
	}

	/**
	 * Load catalog settings
	 * @return void
	 */
	private function loadSettings()
	{
		$settings = Settings::find()->one();
		if ($settings === null)
			$settings = new Settings;

		Yii::$app->params['catalogSettings'] = $settings;
	}

}
