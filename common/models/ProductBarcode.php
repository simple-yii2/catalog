<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class ProductBarcode extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_product_barcode';
    }

}
