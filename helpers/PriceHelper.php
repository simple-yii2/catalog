<?php

namespace cms\catalog\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\helpers\CurrencyHelper;
use cms\catalog\models\Currency;

class PriceHelper
{

    /**
     * Render price
     * @param string $tag price container tag
     * @param float $value price
     * @param Currency|null $currency price currency
     * @param Currency|null $destCurrency rendering currency
     * @return string
     */
    public static function render($tag, $value, $currency = null, $destCurrency = null)
    {
        // Formatter
        $formatter = Yii::$app->getFormatter();

        // Destination (rendering) currency
        if ($destCurrency === null) {
            $destCurrency = CurrencyHelper::getApplicationCurrency();
        }
        if ($destCurrency === null) {
            $destCurrency = $currency;
        }

        // Format
        $value = CurrencyHelper::calc($value, $currency);
        $precision = ArrayHelper::getValue($destCurrency, 'precision', 0);
        $r = Html::tag($tag, $formatter->asDecimal($value, $precision));

        // Prefix/suffix
        if ($destCurrency !== null) {
            if (!empty($destCurrency->prefix)) {
                $r = Html::encode($destCurrency->prefix) . '&nbsp;' . $r;
            }
            if (!empty($destCurrency->suffix)) {
                $r .= '&nbsp;' . Html::encode($destCurrency->suffix);
            }
        }

        return $r;
    }

}
