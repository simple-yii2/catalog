<?php

namespace cms\catalog\common\models;

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

        $fields = array_intersect([
            'store_id',
            'serviceName',
            'city',
            'street',
            'house',
            'apartment',
            'entrance',
            'entryphone',
            'floor',
            'recipient',
            'phone',
            'comment',
        ], $value);

        $this->_fields = serialize($fields);
    }

}
