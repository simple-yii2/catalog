<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use dkhlystov\grid\GridView;
use cms\catalog\common\helpers\PriceHelper;

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
    'columns' => [
        'sku',
        [
            'attribute' => 'name',
            'format' => 'html',
            'content' => function ($model) {
                $result = '';

                if (!empty($model->thumb)) {
                    $result .= Html::img($model->thumb, ['align' => 'left', 'height' => 40, 'hspace' => 10]);
                }

                $result .= Html::encode($model->getTitle());

                if ($model->imageCount > 0) {
                    $result .= '&nbsp;' . Html::tag('span', '<span class="glyphicon glyphicon-picture"></span>&nbsp;' . $model->imageCount, ['class' => 'badge']);
                }

                if (!empty($model->vendor)) {
                    $result .= '&nbsp;' . Html::tag('span', Html::encode($model->vendor), ['class' => 'label label-info']);
                }

                if ($model->category !== null) {
                    $result .= '<br>' . Html::tag('span', $model->category->path, ['class' => 'text-muted']);
                }

                return $result;
            },
        ],
        [
            'attribute' => 'price',
            'format' => 'html',
            'value' => function ($model) {
                return PriceHelper::render('span', $model->price, $model->currency);
            },
        ],
        'quantity',
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 25px;'],
            'template' => '{update}',
        ],
    ],
]) ?>
