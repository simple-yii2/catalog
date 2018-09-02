<?php

namespace cms\catalog\common\models;

use yii\db\ActiveRecord;

class ProductProperty extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_product_property';
    }

    /**
     * Category property relation
     * @return ActiveQueryInterface
     */
    public function getCategoryProperty()
    {
        return $this->hasOne(CategoryProperty::className(), ['id' => 'property_id']);
    }

}
