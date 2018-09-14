<?php

use yii\grid\GridView;
use yii\helpers\Html;

$title = Yii::t('catalog', 'Vendors');

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
    'filterModel' => $model,
    'summary' => '',
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'html',
            'content' => function($model, $key, $index, $column) {
                $result = '';

                if (!empty($model->thumb))
                    $result .= Html::img($model->thumb, ['height' => 20]) . '&nbsp;';

                $result .= Html::encode($model->name);

                return $result;
            },
        ],
        [
            'attribute' => 'url',
            'format' => 'html',
            'content' => function($model, $key, $index, $column) {
                if (empty($model->url))
                    return null;

                return Html::a($model->url);
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 50px;'],
            'template' => '{update} {delete}',
        ],
    ],
]) ?>
