<?php

use yii\helpers\Html;

$title = $form->getObject()->name;

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('catalog', 'Vendors'), 'url' => ['index']],
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
    'form' => $form,
]) ?>
