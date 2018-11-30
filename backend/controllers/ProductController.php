<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;
use yii\web\Controller;
use cms\catalog\backend\filters\ProductFilter;
use cms\catalog\backend\forms\ProductForm;
use cms\catalog\models\Product;
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
     * Disable csrf validation for image and file uploading
     */
    public function beforeAction($action)
    {
        if ($action->id == 'image' || $action->id == 'file') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * List
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();
        if ($category_id = $request->post('category_id')) {
            return $this->redirect(array_merge([''], $request->getQueryParams(), ['category_id' => $category_id]));
        }
        $category_id = $request->get('category_id');

        $model = new ProductFilter;
        if ($category_id != -1) {
            $model->category_id = $category_id;
        }
        $model->load($request->get());

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Create
     * @return string
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();

        $object = new Product;
        $model = new ProductForm(['category_id' => $request->get('category_id')]);

        if ($model->load($request->post()) && $model->validate()) {
            $model->assignTo($object);
            $object->saveWithRelated(false, ['images', 'barcodes']);
            $object->category->updateProductCount();
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index', 'category_id' => $object->category_id]);
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
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $category = $object->category;

        $model = new ProductForm;
        $model->assign($object);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->assignTo($object);
            $object->saveWithRelated(false, ['images', 'barcodes']);
            $object->category->updateProductCount();
            if ($category->id != $object->category->id) {
                $category->updateProductCount();
            }
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index', 'category_id' => $object->category_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'object' => $object,
        ]);
    }

    // /**
    //  * Delete
    //  * @param integer $id
    //  * @return string
    //  */
    // public function actionDelete($id)
    // {
    //     $object = Product::findOne($id);
    //     if ($object === null) {
    //         throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
    //     }

    //     //barcodes
    //     foreach ($object->barcodes as $item) {
    //         $item->delete();
    //     }

    //     //properties
    //     foreach ($object->properties as $item) {
    //         $item->delete();
    //     }

    //     //images
    //     foreach ($object->images as $item) {
    //         Yii::$app->storage->removeObject($item);
    //         $item->delete();
    //     }

    //     //recommended
    //     foreach ($object->recommended as $item) {
    //         $item->delete();
    //     }

    //     //store quantity
    //     foreach ($object->stores as $item) {
    //         $item->delete();
    //     }

    //     //product
    //     $category = $object->category;
    //     if ($object->delete()) {
    //         $category->updateProductCount();
    //         Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
    //     }

    //     $url = ['index'];
    //     $category_id = Yii::$app->getRequest()->get('category_id');
    //     if ($category_id) {
    //         $url['category_id'] = $category_id;
    //     }
    //     return $this->redirect($url);
    // }

    // /**
    //  * Properties update needed when category is changed
    //  * @param integer|null $id
    //  * @return string
    //  */
    // public function actionProperties($id = null)
    // {
    //     $model = new ProductForm(Product::findOne($id));

    //     $model->load(Yii::$app->getRequest()->post());

    //     return $this->renderAjax('form', [
    //         'model' => $model,
    //     ]);
    // }

    // /**
    //  * Product autocomplete
    //  * @return string
    //  */
    // public function actionRecommendedProduct()
    // {
    //     $query = Product::find()
    //         ->andFilterWhere(['like', 'name', Yii::$app->getRequest()->get('term')])
    //         ->orderBy(['name' => SORT_ASC])
    //         ->limit(10);

    //     $items = [];
    //     foreach ($query->all() as $object) {
    //         $items[] = [
    //             'id' => $object->id,
    //             'label' => $object->name,
    //         ];
    //     }

    //     return Json::encode($items);
    // }

    // /**
    //  * Add recommended and render it
    //  * @param integer $id 
    //  * @param integer $recommended_id 
    //  * @return string
    //  */
    // public function actionRecommendedAdd($id, $recommended_id)
    // {
    //     $object = Product::findOne($id);
    //     if ($object === null) {
    //         throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
    //     }

    //     $item = Product::findOne($recommended_id);
    //     if ($item === null) {
    //         throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
    //     }

    //     $model = new ProductForm($object);
    //     $model->recommended = [$item];

    //     return Json::encode([
    //         'content' => $this->renderAjax('form', ['model' => $model]),
    //     ]);
    // }

    /**
     * Image upload
     * @return string
     */
    public function actionImage()
    {
        $name = Yii::$app->storage->prepare('file', [
            'image/png',
            'image/jpg',
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
        ]);

        if ($name === false) {
            throw new BadRequestHttpException(Yii::t('cms', 'Error occurred while image uploading.'));
        }

        return Json::encode([
            ['filelink' => $name],
        ]);
    }

    /**
     * File upload
     * @return string
     */
    public function actionFile()
    {
        $name = Yii::$app->storage->prepare('file');

        if ($name === false) {
            throw new BadRequestHttpException(Yii::t('cms', 'Error occurred while file uploading.'));
        }

        return Json::encode([
            ['filelink' => $name, 'filename' => urldecode(basename($name))],
        ]);
    }

}
