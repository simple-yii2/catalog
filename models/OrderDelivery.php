<?php

namespace cms\catalog\models;

use dkhlystov\db\ActiveRecord;
use cms\catalog\delivery\Delivery;

class OrderDelivery extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_order_delivery';
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

        $fields = array_intersect(array_keys(Delivery::getFieldNames()), $value);

        $this->_fields = serialize($fields);
    }

}
