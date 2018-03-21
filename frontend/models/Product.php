<?php

namespace cms\catalog\frontend\models;

class Product extends \cms\catalog\common\models\Product
{

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->andWhere(['active' => true]);
    }

}
