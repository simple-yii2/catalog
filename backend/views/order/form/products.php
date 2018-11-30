<?php

use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use dkhlystov\grid\GridView;
use cms\catalog\backend\forms\OrderProductForm;
use cms\catalog\models\Order;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\common\helpers\CurrencyHelper;

$orderForm = $model;

$order = new Order;
$orderForm->assignTo($order);
$order->calc();

$currency = CurrencyHelper::getCurrency($orderForm->currency_id);
$productForm = new OrderProductForm(['formName' => Html::getInputName($model, 'products') . '[]']);

?>
<div class="form-group">
    <div class="col-sm-12">
        <?= GridView::widget([
            'id' => 'product-list',
            'dataProvider'=> new ArrayDataProvider([
                'allModels' => array_merge($model->products, [$productForm]),
                'pagination' => false,
            ]),
            'emptyText' => false,
            'layout' => '{items}',
            'options' => ['class' => 'grid-view', 'data-url-product' => Url::toRoute('product'), 'data-url-calc' => Url::toRoute('product-calc')],
            'showFooter' => true,
            'columns' => [
                [
                    'header' => Yii::t('catalog', 'Name'),
                    'footer' => Yii::t('catalog', 'Total'),
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $options = [];
                        if ($model->hasErrors('name')) {
                            Html::addCssClass($options, 'has-error');
                        }
                        return $options;
                    },
                    'content' => function ($model, $key, $index, $column) use ($productForm) {
                        $id = Html::activeHiddenInput($model, 'id', ['disabled' => $model === $productForm]);
                        $product_id = Html::activeHiddenInput($model, 'product_id', ['disabled' => $model === $productForm]);
                        return $id . $product_id . AutoComplete::widget([
                            'model' => $model,
                            'attribute' => 'name',
                            'options' => ['class' => 'p-name form-control', 'data-value' => $model->name, 'title' => $model->name, 'disabled' => $model === $productForm],
                        ]);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Count'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $options = [];
                        if ($model->hasErrors('count')) {
                            Html::addCssClass($options, 'has-error');
                        }
                        return $options;
                    },
                    'content' => function ($model, $key, $index, $column) use ($productForm) {
                        return Html::activeTextInput($model, 'count', ['class' => 'p-count form-control', 'disabled' => $model === $productForm]);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Price'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $options = [];
                        if ($model->hasErrors('price')) {
                            Html::addCssClass($options, 'has-error');
                        }
                        return $options;
                    },
                    'content' => function ($model, $key, $index, $column) use ($productForm) {
                        return Html::activeTextInput($model, 'price', ['class' => 'p-price form-control', 'disabled' => $model === $productForm]);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Amount'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => ['class' => 'product-amount'],
                    'footer' => PriceHelper::render('span', $order->productAmount, $currency, $currency),
                    'footerOptions' => ['class' => 'o-product-amount'],
                    'content' => function ($model, $key, $index, $column) use ($currency) {
                        $value = PriceHelper::render('span', $model->amount, $currency, $currency);
                        return Html::tag('span', $value, ['class' => 'p-amount']);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Discount'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $options = [];
                        if ($model->hasErrors('discount')) {
                            Html::addCssClass($options, 'has-error');
                        }
                        return $options;
                    },
                    'content' => function ($model, $key, $index, $column) use ($orderForm, $productForm) {
                        return Html::activeTextInput($model, 'discount', ['class' => 'p-discount form-control', 'placeholder' => (integer) $orderForm->discount, 'disabled' => $model === $productForm]);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Discount amount'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => ['class' => 'product-discount-amount'],
                    'footer' => PriceHelper::render('span', $order->discountAmount, $currency, $currency),
                    'footerOptions' => ['class' => 'o-discount-amount'],
                    'content' => function ($model, $key, $index, $column) use ($currency) {
                        $value = PriceHelper::render('span', $model->discountAmount, $currency, $currency);
                        return Html::tag('span', $value, ['class' => 'p-discountamount']);
                    },
                ],
                [
                    'header' => Yii::t('catalog', 'Total'),
                    'headerOptions' => ['class' => 't-amount'],
                    'options' => ['class' => 't-amount'],
                    'contentOptions' => ['class' => 'product-total-amount'],
                    'footer' => PriceHelper::render('span', $order->subtotalAmount, $currency, $currency),
                    'footerOptions' => ['class' => 'o-subtotal-amount'],
                    'content' => function ($model, $key, $index, $column) use ($currency) {
                        $value = PriceHelper::render('span', $model->totalAmount, $currency, $currency);
                        return Html::tag('span', $value, ['class' => 'p-totalamount']);
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'options' => ['style' => 'width:25px;'],
                    'template' => '{remove}',
                    'buttons' => [
                        'remove' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-remove"></span>', '#', ['class' => 'product-remove', 'data-confirm' => Yii::t('catalog', 'Remove product?')]);
                        },
                    ],
                ],
            ],
        ]) ?>
        <div><?= Html::button(Yii::t('cms', 'Add'), ['class' => 'product-add btn btn-default']) ?></div>
    </div>
</div>
