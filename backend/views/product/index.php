<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dkhlystov\grid\GridView;
use cms\catalog\backend\assets\ProductListAsset;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Product;

ProductListAsset::register($this);

$title = Yii::t('catalog', 'Products');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    $title,
];

//create url
$createUrl = ['create'];
if (!empty($filter->category_id)) {
    $createUrl['category_id'] = $filter->category_id;
}

//categories
$categories = [-1 => '[' . $filter->getAttributeLabel('category_id') . ']'];
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
            <?= Html::dropDownList('category_id', $filter->category_id, $categories, ['class' => 'form-control']) ?>
        </div>
    <?php ActiveForm::end() ?>
</div>

<?= GridView::widget([
    'dataProvider' => $filter->getDataProvider(),
    'filterModel' => $filter,
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
            'urlCreator' => function ($action, $model, $key, $index, $column) use ($filter) {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                if (!empty($filter->category_id)) {
                    $params['category_id'] = $filter->category_id;
                }
                $params[0] = $column->controller ? $column->controller . '/' . $action : $action;
                return Url::toRoute($params);
            },
        ],
    ],
]) ?>
