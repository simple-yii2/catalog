<?php

use yii\helpers\Html;

// Title
$title = $object->title;
$this->title = $title . ' | ' . Yii::$app->name;

// Breadcrumbs
$breadcrumbs = [
    Yii::t('catalog', 'Catalog'),
    ['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
foreach ($parents as $object) {
    if (!$object->isRoot()) {
        $breadcrumbs[] = $object->title;
    }
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
    'model' => $model,
    'id' => $id,
]) ?>
