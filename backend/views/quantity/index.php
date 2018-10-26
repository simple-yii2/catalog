<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use dkhlystov\grid\GridView;
// use cms\catalog\common\models\Store;

$title = Yii::t('catalog', 'Product quantity');
$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= GridView::widget([
    'dataProvider' => $model->getDataProvider(),
    'filterModel' => $model,
    'summary' => '',
    'tableOptions' => ['class' => 'table table-condensed'],
    'columns' => [
        [
            'attribute' => 'name',
            'value' => function ($model) {
                return $model->getTitle();
            }
        ],
        'quantity',
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 25px;'],
            'template' => '{update}',
        ],
    ],
]) ?>
