<?php

use yii\widgets\ListView;
use cms\catalog\frontend\widgets\OfferItem;

?>
<?= ListView::widget([
	'dataProvider' => $model->getDataProvider(),
	'layout' => '<div class="row">{items}</div>{pager}',
	'itemOptions' => ['class' => 'col-sm-4'],
	'itemView' => function($model, $key, $index, $widget) {
		return OfferItem::widget(['model' => $model]);
	},
]) ?>
