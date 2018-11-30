<?php

namespace cms\catalog\delivery;

use Yii;
use yii\base\BaseObject;
use cms\catalog\models\Order;

class Delivery extends BaseObject
{

    /**
     * Filed names
     * @return array
     */
    public static function getFieldNames()
    {
        return [
            'store_id' => Yii::t('catalog', 'Store'),
            'serviceName' => Yii::t('catalog', 'Service name'),
            'city' => Yii::t('catalog', 'City'),
            'street' => Yii::t('catalog', 'Street'),
            'house' => Yii::t('catalog', 'House'),
            'apartment' => Yii::t('catalog', 'Apartment'),
            'entrance' => Yii::t('catalog', 'Entrance'),
            'entryphone' => Yii::t('catalog', 'Entryphone'),
            'floor' => Yii::t('catalog', 'Floor'),
            'recipient' => Yii::t('catalog', 'Recipient'),
            'phone' => Yii::t('catalog', 'Phone'),
            'trackingCode' => Yii::t('catalog', 'Tracking code'),
        ];
    }

    /**
     * Delivery method name getter
     * @return string
     */
    public static function getName()
    {
        throw new \Exception('"getName" function must be implimented.');
    }

    /**
     * Fields
     * @return array
     */
    public static function getFields()
    {
        return array_keys(self::getFieldNames());
    }

    /**
     * Fields getter
     * @return array
     */
    public static function getAvailableFields()
    {
        return [];
    }

    /**
     * Delivery method availability checking
     * @param Order $order 
     * @return boolean
     */
    public static function isAvailable(Order $order)
    {
        return true;
    }

    /**
     * Price calculation
     * @param Order $order 
     * @return float
     */
    public static function getPrice(Order $order)
    {
        throw new \Exception('"getPrice" function must be implimented.');
    }

    /**
     * Days count calculation
     * @param Order $order 
     * @return integer
     */
    public static function getDays(Order $order)
    {
        throw new \Exception('"getDays" function must be implimented.');
    }

}
