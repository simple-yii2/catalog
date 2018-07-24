<?php

use yii\helpers\Html;

$title = Yii::t('catalog', 'Create product/service');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('catalog', 'Goods/Services'), 'url' => ['index']],
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
    'form' => $form,
]) ?>
