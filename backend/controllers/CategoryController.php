<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

use cms\catalog\backend\models\CategoryForm;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Goods;

class CategoryController extends Controller
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
	 * Tree
	 * @param integer|null $id Initial item id
	 * @return string
	 */
	public function actionIndex($id = null)
	{
		$initial = Category::findOne($id);

		$dataProvider = new ActiveDataProvider([
			'query' => Category::find(),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'initial' => $initial,
		]);
	}

	/**
	 * Create
	 * @param integer|null $id Parent id
	 * @return string
	 */
	public function actionCreate($id = null)
	{
		$parent = Category::findOne($id);
		if ($parent === null)
			$parent = Category::find()->roots()->one();

		$model = new CategoryForm(new Category);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save($parent)) {
			$this->updateGoods();

			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect([
				'index',
				'id' => $model->getObjectId(),
			]);
		}

		return $this->render('create', [
			'model' => $model,
			'id' => $id,
		]);
	}

	/**
	 * Update
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$object = Category::findOne($id);
		if ($object === null || $object->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$model = new CategoryForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect([
				'index',
				'id' => $model->getObjectId(),
			]);
		}

		return $this->render('update', [
			'model' => $model,
			'id' => $object->id,
		]);
	}

	/**
	 * Delete
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$object = Category::findOne($id);
		if ($object === null || $object->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$sibling = $object->prev()->one();
		if ($sibling === null)
			$sibling = $object->next()->one();

		if ($object->deleteWithChildren())
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Item deleted successfully.'));

		return $this->redirect(['index', 'id' => $sibling ? $sibling->id : null]);
	}

	/**
	 * Move
	 * @param integer $id 
	 * @param integer $target 
	 * @param integer $position 
	 * @return void
	 */
	public function actionMove($id, $target, $position)
	{
		$object = Category::findOne($id);
		if ($object === null || $object->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$t = Category::findOne($target);
		if ($t === null || $t->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		switch ($position) {
			case 0:
				$object->insertBefore($t);
				break;

			case 1:
				$object->appendTo($t);
				break;
			
			case 2:
				$object->insertAfter($t);
				break;
		}

		$object->refresh();
		$object->updatePath();

		$this->updateGoods();
	}

	private function updateGoods()
	{
		$query = Category::find()->select(['id', 'lft', 'rgt'])->asArray();
		foreach ($query->all() as $row) {
			Goods::updateAll([
				'category_lft' => $row['lft'],
				'category_rgt' => $row['rgt'],
			], [
				'category_id' => $row['id'],
			]);
		}
	}

}
