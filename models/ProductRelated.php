<?php

namespace cms\catalog\models;

use dkhlystov\db\ActiveRecord;

class ProductRelated extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_product_recommended';
    }

}
