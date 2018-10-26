<?php

use yii\helpers\Html;

$title = $model->getObject()->getTitle();

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    ['label' => Yii::t('catalog', 'Product quantity'), 'url' => ['index']],
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
    'model' => $model,
]) ?>
