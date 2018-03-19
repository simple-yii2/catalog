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
     * @var Currency[]
     */
    private static $_currencies;

    /**
     * Calculate price in application currency
     * @param float $value 
     * @param Currency|null $currency 
     * @return float
     */
    public static function calcPrice($value, $currency = null)
    {
        $appCurrency = CurrencyHelper::getCurrency();

        if ($appCurrency !== null && $currency !== null && $appCurrency->id != $currency->id) {
            $value = round($value * $currency->rate / $appCurrency->rate, $appCurrency->precision);
        }

        return $value;
    }

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
        $c = CurrencyHelper::getCurrency();
        if ($c === null) {
            $c = $currency;
        }

        //format
        $value = self::calcPrice($value, $currency);
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

    /**
     * Get currency by id
     * @param integer $id 
     * @return Currency|null
     */
    public static function getCurrency($id)
    {
        //init currencies if needed
        if (self::$_currencies === null) {
            $items = [];
            foreach (Currency::find()->all() as $item) {
                $items[$item->id] = $item;
            }
            self::$_currencies = $items;
        }

        return ArrayHelper::getValue(self::$_currencies, $id);
    }

}
