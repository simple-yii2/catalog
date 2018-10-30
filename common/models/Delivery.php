<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveRecord;
use cms\catalog\common\helpers\CurrencyHelper;

class Delivery extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_delivery';
    }

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
            'comment' => Yii::t('catalog', 'Comment'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct(array_replace([
            'currency_id' => CurrencyHelper::getApplicationCurrencyId(),
        ], $config));
    }

    /**
     * Currency relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * Fields getter
     * @return array
     */
    public function getAvailableFields()
    {
        $fields = @unserialize($this->_fields);
        return $fields === false ? [] : $fields;
    }

    /**
     * Fields setter
     * @param array $value 
     * @return void
     */
    public function setAvailableFields($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $fields = array_intersect(array_keys(self::getFieldNames()), $value);

        $this->_fields = serialize($fields);
    }

}
