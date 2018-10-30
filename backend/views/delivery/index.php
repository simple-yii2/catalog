<?php

use yii\helpers\Html;
use dkhlystov\grid\GridView;
use cms\catalog\common\helpers\PriceHelper;

$title = Yii::t('catalog', 'Delivery methods');
$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
    <?= Html::a(Yii::t('cms', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?= GridView::widget([
    'dataProvider' => $model->getDataProvider(),
    'summary' => '',
    'columns' => [
        'name',
        [
            'attribute' => 'price',
            'format' => 'html',
            'value' => function ($model) {
                return PriceHelper::render('span', $model->price, $model->currency);
            },
        ],
        'days',
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 50px;'],
            'template' => '{update} {delete}',
        ],
    ],
]) ?>
