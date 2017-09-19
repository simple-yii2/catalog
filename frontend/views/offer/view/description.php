<?php

use yii\helpers\Html;

$title = Yii::t('catalog', 'Description');

$description = nl2br(Html::encode($model->description));

?>
<h2><?= Html::encode($title) ?></h2>
<div class="offer-description"><?= $description ?></div>
