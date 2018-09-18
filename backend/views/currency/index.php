<?php

use yii\helpers\Html;
use dkhlystov\grid\GridView;

$title = Yii::t('catalog', 'Currencies');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
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
        [
            'attribute' => 'name',
            'content' => function ($model) {
                $name = Html::encode($model->name);
                if ($model->default) {
                    $name .= ' ' . Html::tag('span', Yii::t('catalog', 'Default'), ['class' => 'label label-primary']);
                }
                return $name;
            },
        ],
        'code',
        'rate',
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 50px;'],
            'template' => '{update} {delete}',
        ],
    ],
]) ?>
