<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;

use cms\catalog\backend\models\ProductForm;
use cms\catalog\backend\models\ProductSearch;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\Settings;

class ProductController extends Controller
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
	 * @inheritdoc
	 */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action) !== true)
			return false;

		$this->loadSettings();

		return true;
	}

	/**
	 * List
	 * @return string
	 */
	public function actionIndex()
	{
		$request = Yii::$app->getRequest();

		$filter = new ProductSearch;
		if ($filter->load($request->post())) {
			return $this->redirect(array_merge_recursive([''], $request->getQueryParams(), [$filter->formName() => $filter->getDirtyAttributes()]));
		}
		$filter->load($request->get());

		return $this->render('index', [
			'search' => $filter,
		]);
	}

	/**
	 * Create
	 * @return string
	 */
	public function actionCreate()
	{
		$model = new ProductForm;

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			$model->getObject()->category->updateProductCount();

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
		$object = Product::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$category = $object->category;

		$model = new ProductForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			$object->category->updateProductCount();

			if ($category->id != $object->category->id)
				$category->updateProductCount();

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
		$object = Product::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		//barcodes
		foreach ($object->barcodes as $item)
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


		//product
		$category = $object->category;
		if ($object->delete()) {
			$category->updateProductCount();

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
		$model = new ProductForm(Product::findOne($id));

		$model->load(Yii::$app->getRequest()->post());

		return $this->renderAjax('form', [
			'model' => $model,
		]);
	}

	/**
	 * Product autocomplete
	 * @return string
	 */
	public function actionRecommendedProduct()
	{
		$query = Product::find()
			->andFilterWhere(['like', 'name', Yii::$app->getRequest()->get('term')])
			->orderBy(['name' => SORT_ASC])
			->limit(10);

		$items = [];
		foreach ($query->all() as $object) {
			$items[] = [
				'id' => $object->id,
				'label' => $object->name,
			];
		}

		return Json::encode($items);
	}

	/**
	 * Add recommended and render it
	 * @param integer $id 
	 * @param integer $recommended_id 
	 * @return string
	 */
	public function actionRecommendedAdd($id, $recommended_id)
	{
		$object = Product::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$item = Product::findOne($recommended_id);
		if ($item === null)
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$model = new ProductForm($object);
		$model->recommended = [$item];

		return Json::encode([
			'content' => $this->renderAjax('form', ['model' => $model]),
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
