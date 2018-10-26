<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class StoreProduct extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_store_product';
    }

}
