<?php

use cms\catalog\frontend\widgets\OfferFilter;

?>
<?= OfferFilter::widget([
	'model' => $model,
	'buttonText' => Yii::t('catalog', 'Apply'),
	'trueText' => Yii::t('catalog', 'Yes'),
	'falseText' => Yii::t('catalog', 'No'),
]) ?>
