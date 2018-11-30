<?php

namespace cms\catalog\models;

use dkhlystov\db\ActiveRecord;
use cms\user\common\models\User;

class OrderCustomer extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_order_customer';
    }

    /**
     * User relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
