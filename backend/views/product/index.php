<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dkhlystov\grid\GridView;
use cms\catalog\backend\assets\ProductListAsset;
use cms\catalog\helpers\PriceHelper;
use cms\catalog\models\Category;
use cms\catalog\models\Product;

ProductListAsset::register($this);

$filterModel = $model;

// Title
$title = Yii::t('catalog', 'Products');
$this->title = $title . ' | ' . Yii::$app->name;

// Breadcrumbs
$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    $title,
];

// Create url
$createUrl = ['create'];
if (!empty($model->category_id)) {
    $createUrl['category_id'] = $model->category_id;
}

// Categories
$categories = [-1 => '[' . $model->getAttributeLabel('category_id') . ']'];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
    if ($item->isLeaf()) {
        $categories[$item->id] = $item->path;
    }
}

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
    <?= Html::a(Yii::t('cms', 'Create'), $createUrl, ['class' => 'btn btn-primary']) ?>
</div>

<div>
    <?php $activeForm = ActiveForm::begin([
        'enableClientValidation' => false,
    ]) ?>
        <div class="form-group">
            <?= Html::dropDownList('category_id', $model->category_id, $categories, ['class' => 'form-control']) ?>
        </div>
    <?php ActiveForm::end() ?>
</div>

<?= GridView::widget([
    'dataProvider' => $model->getDataProvider(),
    'filterModel' => $model,
    'summary' => '',
    'rowOptions' => function ($model, $key, $index, $grid) {
        return !$model->active ? ['class' => 'warning'] : [];
    },
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

                $result .= Html::encode($model->name);

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
                return $model->price === null ? null : PriceHelper::render('span', $model->price, $model->currency);
            },
        ],
        [
            'attribute' => 'availability',
            'filter' => Product::getAvailabilityNames(),
            'value' => function ($model, $key, $index, $column) {
                return $model->getAvailabilityName();
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width: 50px;'],
            'template' => '{update} {delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) use ($filterModel) {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                if (!empty($filterModel->category_id)) {
                    $params['category_id'] = $filterModel->category_id;
                }
                $params[0] = $column->controller ? $column->controller . '/' . $action : $action;
                return Url::toRoute($params);
            },
        ],
    ],
]) ?>
