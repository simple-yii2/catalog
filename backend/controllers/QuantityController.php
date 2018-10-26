<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\ProductQuantityFilter;
use cms\catalog\backend\forms\ProductQuantityForm;
use cms\catalog\common\models\Product;

class QuantityController extends Controller
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
        $model = new ProductQuantityFilter;
        $model->load(Yii::$app->getRequest()->get());

        return $this->render('index', ['model' => $model]);
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

        $model = new ProductQuantityForm($object);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

}
