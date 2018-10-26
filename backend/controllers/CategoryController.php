<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\CategoryFilter;
use cms\catalog\backend\forms\CategoryForm;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Product;

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
        $model = new CategoryFilter;
        $model->load(Yii::$app->getRequest()->get());

        return $this->render('index', [
            'model' => $model,
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
        if ($parent === null) {
            $parent = Category::find()->roots()->one();
        }

        if ($parent->productCount > 0) {
            throw new BadRequestHttpException(Yii::t('cms', 'Operation not permitted.'));
        }

        $maxDepth = $this->module->maxCategoryDepth;
        if ($maxDepth !== null && $parent->depth >= $maxDepth) {
            throw new BadRequestHttpException(Yii::t('cms', 'Operation not permitted.'));
        }

        $model = new CategoryForm(null, [
            'properties' => array_map(function ($v) {
                $v->readOnly = true;
                return $v;
            }, array_merge($parent->getParentProperties(), $parent->properties)),
        ]);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save($parent)) {
            $this->updateProducts();
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index', 'id' => $model->getObject()->id]);
        }

        return $this->render('create', [
            'model' => $model,
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
        $object = Category::findOne($id);
        if ($object === null || $object->isRoot()) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $model = new CategoryForm($object);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index', 'id' => $model->getObject()->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $object->id,
            'parents' => $object->parents()->all(),
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
        if ($object === null || $object->isRoot()) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        if ($object->productCount > 0) {
            throw new BadRequestHttpException(Yii::t('cms', 'Operation not permitted.'));
        }

        $initial = $object->prev()->one();
        if ($initial === null) {
            $initial = $object->next()->one();
        }
        if ($initial === null) {
            $initial = $object->parents(1)->one();
        }

        if ($object->deleteWithChildren()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
        }

        return $this->redirect(['index', 'id' => $initial ? $initial->id : null]);
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
        if ($object === null || $object->isRoot()) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $t = Category::findOne($target);
        if ($t === null || $t->isRoot()) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        if ($position == 1 && $t->productCount > 0) {
            throw new BadRequestHttpException(Yii::t('cms', 'Operation not permitted.'));
        }

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
        $object->updateAliasAndPath();

        $this->updateProducts();
    }

    /**
     * Common properties
     * @return string
     */
    public function actionProperties()
    {
        $model = new CategoryForm(Category::find()->roots()->one());

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('properties', [
            'model' => $model,
        ]);
    }

    /**
     * Update category data in product objects
     * @return void
     */
    private function updateProducts()
    {
        $query = Category::find()->select(['id', 'lft', 'rgt'])->asArray();
        foreach ($query->all() as $row) {
            Product::updateAll([
                'category_lft' => $row['lft'],
                'category_rgt' => $row['rgt'],
            ], [
                'category_id' => $row['id'],
            ]);
        }
    }

}
