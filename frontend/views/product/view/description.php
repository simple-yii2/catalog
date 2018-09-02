<?php

use yii\helpers\Html;

$title = Yii::t('catalog', 'Description');

?>
<h2><?= Html::encode($title) ?></h2>
<div class="product-description"><?= $model->description ?></div>
