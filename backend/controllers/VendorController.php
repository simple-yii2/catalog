<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\VendorFilter;
use cms\catalog\backend\forms\VendorForm;
use cms\catalog\common\models\Vendor;

class VendorController extends Controller
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
        $model = new VendorFilter;
        $model->load(Yii::$app->getRequest()->get());

        return $this->render('index', ['model' => $model]);
    }

    /**
     * Create
     * @return string
     */
    public function actionCreate()
    {
        $form = new VendorForm;

        if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('create', ['form' => $form]);
    }

    /**
     * Update
     * @param integer $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $object = Vendor::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $form = new VendorForm($object);

        if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('update', ['form' => $form]);
    }

    /**
     * Delete
     * @param integer $id
     * @return string
     */
    public function actionDelete($id)
    {
        $object = Vendor::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        if ($object->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
        }

        return $this->redirect(['index']);
    }

}
