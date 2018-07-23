<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\CurrencyFilter;
use cms\catalog\backend\forms\CurrencyForm;
use cms\catalog\common\models\Currency;

class CurrencyController extends Controller
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
        $filter = new CurrencyFilter;
        $filter->load(Yii::$app->getRequest()->get());

        return $this->render('index', ['filter' => $filter]);
    }

    /**
     * Create
     * @return string
     */
    public function actionCreate()
    {
        $form = new CurrencyForm;

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
        $object = Currency::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $form = new CurrencyForm($object);

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
        $object = Currency::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        if ($object->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
        }

        return $this->redirect(['index']);
    }

}
