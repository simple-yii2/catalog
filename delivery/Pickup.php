<?php

namespace cms\catalog\delivery;

use Yii;
use cms\catalog\models\Order;

class Pickup extends Delivery
{

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return Yii::t('catalog', 'Pickup');
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableFields()
    {
        return ['store_id'];
    }

    /**
     * @inheritdoc
     */
    public static function getPrice(Order $order)
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public static function getDays(Order $order)
    {
        return 0;
    }

}
