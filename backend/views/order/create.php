<?php

use yii\helpers\Html;

$title = Yii::t('cms', 'Create');
$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
    ['label' => Yii::t('catalog', 'Orders'), 'url' => ['index']],
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', ['model' => $model]) ?>
