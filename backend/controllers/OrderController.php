<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use cms\catalog\backend\filters\OrderFilter;
use cms\catalog\backend\forms\OrderForm;
use cms\catalog\backend\forms\OrderProductForm;
use cms\catalog\common\helpers\DeliveryHelper;
use cms\catalog\common\helpers\CurrencyHelper;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Product;
use cms\catalog\models\Order;
use cms\user\common\models\User;

class OrderController extends Controller
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
        $model = new OrderFilter;
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
        $object = new Order;
        $model = new OrderForm;
        $model->number = Order::generateNumber();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->assignTo($object);
            $object->calc();
            $object->saveWithRelated(false, ['customer', 'delivery', 'products']);
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
        $object = Order::findOne($id);
        if ($object === null) {
            throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
        }

        $model = new OrderForm;
        $model->assign($object);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->assignTo($object);
            $object->calc();
            $object->saveWithRelated(false, ['customer', 'delivery', 'products']);
            Yii::$app->session->setFlash('success', Yii::t('cms', 'Changes saved successfully.'));
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model, 'object' => $object]);
    }

    /**
     * Delete
     * @param integer $id
     * @return string
     */
    // public function actionDelete($id)
    // {
    //     $object = Delivery::findOne($id);
    //     if ($object === null) {
    //         throw new BadRequestHttpException(Yii::t('cms', 'Item not found.'));
    //     }

    //     if ($object->delete()) {
    //         Yii::$app->session->setFlash('success', Yii::t('cms', 'Item deleted successfully.'));
    //     }

    //     return $this->redirect(['index']);
    // }

    /**
     * Customer autocomplete
     * @return string
     */
    public function actionCustomer()
    {
        $term = Yii::$app->getRequest()->get('term');

        // Search query
        $query = User::find()->andWhere(['or',
            ['like', 'email', $term],
            ['like', 'firstName', $term],
            ['like', 'lastName', $term],
        ])->limit(16);
        $query->andWhere(['<>', 'email', 'admin']);

        return Json::encode(array_map(function ($v) {return ['id' => $v->id, 'name' => trim($v->lastName . ' ' . $v->firstName), 'email' => $v->email, 'value' => $v->getUsername()];}, $query->all()));
    }

    /**
     * Product autocomplete
     * @return string
     */
    public function actionProduct()
    {
        $request = Yii::$app->getRequest();
        $term = $request->get('term');
        $currency_id = $request->get('currency_id');

        // Order currency
        $currency = CurrencyHelper::getCurrency($currency_id);
        $precision = ArrayHelper::getValue($currency, 'precision', 0);

        // Search query
        $query = Product::search($term)->with('currency')->limit(8);

        return Json::encode(array_map(function ($v) use ($currency, $precision) {return [
            'id' => $v->id,
            'sku' => $v->sku,
            'price' => number_format(CurrencyHelper::calc($v->price, $v->currency, $currency), $precision, '.', ''),
            'value' => $v->getTitle(),
        ];}, $query->all()));
    }

    /**
     * Calculate order
     * @return string
     */
    public function actionCalc()
    {
        // Form
        $model = new OrderForm;
        $model->load(Yii::$app->getRequest()->get());

        // Object
        $object = new Order;
        $model->assignTo($object);

        // Calc
        $object->calc();

        // Order currency
        $currency = CurrencyHelper::getCurrency($object->currency_id);

        // Products
        $products = array_map(function ($item) use ($currency) {
            return [
                'product_id' => $item->product_id,
                'amount' => PriceHelper::render('span', $item->amount, $currency, $currency),
                'discountAmount' => PriceHelper::render('span', $item->discountAmount, $currency, $currency),
                'totalAmount' => PriceHelper::render('span', $item->totalAmount, $currency, $currency),
            ];
        }, $object->products);

        // Response
        return Json::encode([
            'discount' => $object->discount,
            'delivery' =>  DeliveryHelper::getDeliveryData($object),
            'products' => $products,
            'productAmount' => PriceHelper::render('span', $object->productAmount, $currency, $currency),
            'discountAmount' => PriceHelper::render('span', $object->discountAmount, $currency, $currency),
            'subtotalAmount' => PriceHelper::render('span', $object->subtotalAmount, $currency, $currency),
        ]);
    }

}
