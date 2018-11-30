<?php

namespace cms\catalog\common\helpers;

use Yii;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use cms\catalog\models\Order;

class DeliveryHelper
{

    /**
     * @var array
     */
    private static $_classes;

    /**
     * Available delivery classes, according with module config
     * @param Order|null $order 
     * @return array
     */
    public static function getDeliveryClasses(Order $order = null)
    {
        // All classes
        if (self::$_classes === null) {
            // CMS Module
            $module = self::getModule(Yii::$app, 'cms\Module');
            // Catalog module, delivery
            self::$_classes = $module === null ? [] : ArrayHelper::getValue(self::getModule($module, 'cms\catalog\backend\Module'), 'delivery', []);
        }

        // Order not set
        if ($order === null) {
            return self::$_classes;
        }

        // Filtering with order
        $classes = [];
        foreach (self::$_classes as $key => $class) {
            if ($class::isAvailable($order)) {
                $classes[$key] = $class;
            }
        }
        return $classes;
    }

    /**
     * Available delivery methods data (price, days, fields)
     * @param Order|null $order 
     * @return array
     */
    public static function getDeliveryData(Order $order = null)
    {
        //order currency
        $currency = CurrencyHelper::getCurrency($order->currency_id);

        return array_map(function ($class) use ($order, $currency) {
            return [
                'name' => $class::getName(),
                'price' => CurrencyHelper::calc($class::getPrice($order), $currency, $currency),
                'days' => $class::getDays($order),
                'fields' => $class::getAvailableFields(),
            ];
        }, self::getDeliveryClasses($order));
    }

    /**
     * Get delivery class name by key
     * @param string $key 
     * @return string|null
     */
    public static function getDeliveryClass($key)
    {
        return ArrayHelper::getValue(self::getDeliveryClasses(), $key);
    }

    /**
     * Get delivery key for class name
     * @param string $class 
     * @return string|null
     */
    public static function getDeliveryKey($class)
    {
        return ArrayHelper::getValue(array_flip(self::getDeliveryClasses()), $class);
    }

    /**
     * Search module by class name
     * @param Module $parent 
     * @param string $className 
     * @return Module|null
     */
    private static function getModule(Module $parent, $className)
    {
        foreach ($parent->getModules() as $id => $module) {
            if ($module instanceof Module) {
                $name = $module::className();
            } elseif (is_array($module)) {
                $name = ArrayHelper::getValue($module, 'class', '');
            } else {
                $name = (string) $module;
            }

            if ($name == $className) {
                return $parent->getModule($id);
            }
        }

        return null;
    }

}
