<?php

namespace cms\catalog\models;

use yii\helpers\ArrayHelper;
use dkhlystov\db\ActiveRecord;

class OrderProduct extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_order_product';
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct(array_replace([
            'amount' => 0,
            'discountAmount' => 0,
            'totalAmount' => 0,
        ], $config));
    }

    /**
     * Product relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * Calc product
     * @param Order $order 
     * @return void
     */
    public function calc(Order $order)
    {
        // Amount
        $this->amount = $this->price * $this->count;

        // Discount
        $discount = $this->discount === null ? $order->discount : $this->discount;
        $this->discountAmount = round($this->amount * $discount / 100, ArrayHelper::getValue($order, ['currency', 'precision'], 2));

        // Total
        $this->totalAmount = $this->amount - $this->discountAmount;
    }

}
