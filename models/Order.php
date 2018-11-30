<?php

namespace cms\catalog\models;

use Yii;
use yii\helpers\ArrayHelper;
use dkhlystov\db\ActiveRecord;

class Order extends ActiveRecord
{

    //payment
    const NOT_PAID = 0;
    const FULLY_PAID = 1;
    const PARTIALLY_PAID = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_order';
    }

    /**
     * Payment names;
     * @return array
     */
    public static function getPaymentStateNames()
    {
        return [
            self::NOT_PAID => Yii::t('catalog', 'Not paid'),
            self::FULLY_PAID => Yii::t('catalog', 'Fully paid'),
            self::PARTIALLY_PAID => Yii::t('catalog', 'Partially paid'),
        ];
    }

    /**
     * Currency relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * Customer relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getCustomer()
    {
        return $this->hasOne(OrderCustomer::className(), ['order_id' => 'id']);
    }

    /**
     * Delivery relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getDelivery()
    {
        return $this->hasOne(OrderDelivery::className(), ['order_id' => 'id']);
    }

    /**
     * Products relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    /**
     * Calculate order
     * @return void
     */
    public function calc()
    {
        // Products
        $this->productAmount = 0;
        $this->discountAmount = 0;
        $this->subtotalAmount = 0;
        foreach ($this->products as $item) {
            $item->calc($this);
            $this->productAmount += $item->amount;
            $this->discountAmount += $item->discountAmount;
            $this->subtotalAmount += $item->totalAmount;
        }

        // Delivery
        $this->deliveryAmount = 0;
        if ($this->delivery !== null) {
            $this->deliveryAmount = $this->delivery->price;
        }

        // Total
        $this->totalAmount = $this->subtotalAmount + $this->deliveryAmount;
    }

    /**
     * Generate number for next order
     * @return string
     */
    public static function generateNumber()
    {
        return '';
    }

}
