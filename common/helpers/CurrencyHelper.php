<?php

namespace cms\catalog\common\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Settings;

/**
 * Currency helper
 */
class CurrencyHelper {

    //name for currency in cookies
    const COOKIE_NAME = 'currency';

    /**
     * @var integer application currency model id
     */
    private static $_currency_id;

    /**
     * @var Currency[]
     */
    private static $_currencies;

    /**
     * Get application currency id
     * @return integer|null
     */
    public static function getApplicationCurrencyId()
    {
        if (self::$_currency_id !== null) {
            return self::$_currency_id;
        }

        //try to get it from cookies
        $cookies = Yii::$app->getRequest()->getCookies();
        $id = $cookies->getValue(self::COOKIE_NAME);
        if ($id !== null) {
            return self::$_currency_id = $id;
        }

        //try to get it from settings
        $settings = Settings::find()->one();
        if ($settings !== null) {
            return self::$_currency_id = $settings->defaultCurrency_id;
        }

        return null;
    }

    /**
     * Get currency
     * @param integer|null $id currency id
     * @return Currency
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

    /**
     * Get application currency
     * @return Currency
     */
    public static function getApplicationCurrency()
    {
        return self::getCurrency(self::getApplicationCurrencyId());
    }

    /**
     * Set application currency
     * @param Currency $value 
     * @return void
     */
    public static function setApplicationCurrency(Currency $value)
    {
        self::$_currency = $value;

        $cookies = Yii::$app->getResponse()->getCookies();
        $cookies->add(new Cookie([
            'name' => self::COOKIE_NAME,
            'value' => $value->id,
        ]));
    }

    /**
     * Calculate amount into application currency
     * @param float $value 
     * @param Currency|null $currency currency of value
     * @param Currency|null $destCurrency application currency. Application currency used if not set.
     * @return float
     */
    public static function calc($value, $currency = null, $destCurrency = null)
    {
        if ($destCurrency === null) {
            $destCurrency = self::getApplicationCurrency();
        }

        if ($destCurrency !== null && $currency !== null && $destCurrency->id != $currency->id) {
            $value = round($value * $currency->rate / $destCurrency->rate, $destCurrency->precision);
        }

        return $value;
    }

}
