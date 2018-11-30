<?php

namespace cms\catalog\models;

use dkhlystov\db\ActiveRecord;
use yii\db\Expression;

class Currency extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_currency';
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::init(array_replace([
            'precision' => -2,
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Product price value
        if (array_key_exists('rate', $changedAttributes)) {
            Product::updateAll(['priceValue' => new Expression('price * '. $this->rate)], ['currency_id' => $this->id]);
        }

        // Default
        if (array_key_exists('default', $changedAttributes) && $this->default) {
            $this->updateAll(['default' => false], ['<>', 'id', $this->id]);
        }
    }

}
