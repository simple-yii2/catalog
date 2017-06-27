<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

use cms\catalog\backend\models\CategorySearch;
use cms\catalog\backend\models\CategoryForm;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Offer;

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
		return $this->render('index', [
			'search' => new CategorySearch,
			'initial' => Category::findOne($id),
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

		if ($parent->offerCount > 0)
			throw new BadRequestHttpException(Yii::t('catalog', 'Operation not permitted.'));

		$form = new CategoryForm;
		$form->properties = array_merge($parent->getParentProperties(), $parent->properties);

		if ($form->load(Yii::$app->getRequest()->post()) && $form->save($parent)) {
			$this->updateOffers();

			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect([
				'index',
				'id' => $form->getModel()->id,
			]);
		}

		return $this->render('create', [
			'form' => $form,
			'id' => $id,
			'parents' => array_merge($parent->parents()->all(), [$parent]),
		]);
	}

	/**
	 * Update
	 * @param integer $id
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$model = Category::findOne($id);
		if ($model === null || $model->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		$form = new CategoryForm($model);

		if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));
			return $this->redirect([
				'index',
				'id' => $form->getModel()->id,
			]);
		}

		return $this->render('update', [
			'form' => $form,
			'id' => $model->id,
			'parents' => $model->parents()->all(),
		]);
	}

	/**
	 * Delete
	 * @param integer $id
	 * @return string
	 */
	public function actionDelete($id)
	{
		$model = Category::findOne($id);
		if ($model === null || $model->isRoot())
			throw new BadRequestHttpException(Yii::t('catalog', 'Item not found.'));

		if ($model->offerCount > 0)
			throw new BadRequestHttpException(Yii::t('catalog', 'Operation not permitted.'));

		$sibling = $model->prev()->one();
		if ($sibling === null)
			$sibling = $model->next()->one();

		if ($model->deleteWithChildren())
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

		if ($position == 1 && $t->offerCount > 0)
			throw new BadRequestHttpException(Yii::t('catalog', 'Operation not permitted.'));

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

		$this->updateOffers();
	}

	private function updateOffers()
	{
		$query = Category::find()->select(['id', 'lft', 'rgt'])->asArray();
		foreach ($query->all() as $row) {
			Offer::updateAll([
				'category_lft' => $row['lft'],
				'category_rgt' => $row['rgt'],
			], [
				'category_id' => $row['id'],
			]);
		}
	}

}
