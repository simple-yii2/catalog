<?php

namespace cms\catalog\frontend\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Settings;

class PriceHelper
{

    /**
     * Render price
     * @param string $tag price container tag
     * @param string $value price
     * @param Currency|null $currency price currency
     * @return string
     */
    public static function render($tag, $value, $currency = null)
    {
        $formatter = Yii::$app->getFormatter();
        $c = CurrencyHelper::getApplicationCurrency();
        if ($c === null) {
            $c = $currency;
        }

        //format
        $value = CurrencyHelper::calc($value, $currency);
        $precision = ArrayHelper::getValue($c, 'precision', 0);
        $r = Html::tag($tag, $formatter->asDecimal($value, $precision));

        //prefix/suffix
        if ($c !== null) {
            if (!empty($c->prefix))
                $r = Html::encode($c->prefix) . '&nbsp;' . $r;
            if (!empty($c->suffix))
                $r .= '&nbsp;' . Html::encode($c->suffix);
        }

        return $r;
    }

}
