<?php

use yii\helpers\Html;

$title = $category->isRoot() ? Yii::t('catalog', 'Catalog') : $category->title;

?>
<h1><?= Html::encode($title) ?></h1>

<div class="row">
	<div class="col-sm-3"><?= $this->render('index/filter', ['model' => $model]) ?></div>
	<div class="col-sm-9"><?= $this->render('index/list', ['model' => $model]) ?></div>
</div>

