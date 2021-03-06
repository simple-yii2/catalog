<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\DeliveryFilter;
use cms\catalog\backend\forms\DeliveryForm;
use cms\catalog\common\models\Delivery;
// use cms\catalog\common\models\Product;

class DeliveryController extends Controller
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
        $model = new DeliveryFilter;
        $model->load(Yii::$app->getRequest()->get());

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
        $model = new DeliveryForm;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Update
     * @param integer $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $object = Delivery::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $model = new DeliveryForm($object);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Delete
     * @param integer $id
     * @return string
     */
    public function actionDelete($id)
    {
        $object = Delivery::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        if ($object->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
        }

        return $this->redirect(['index']);
    }

}
