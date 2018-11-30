<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use cms\catalog\common\models\Product;

class OrderProductForm extends Form
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $product_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $count = 1;

    /**
     * @var float
     */
    public $price;

    /**
     * @var integer
     */
    public $discount;

    /**
     * @var float
     */
    private $_amount = 0;

    /**
     * @var float
     */
    private $_discountAmount = 0;

    /**
     * @var float
     */
    private $_totalAmount = 0;

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->id = $object->id;
        $this->product_id = $object->product_id;
        $this->name = $object->name;
        $this->count = $object->count;
        $this->price = $object->price;
        $this->discount = $object->discount;
        $this->_amount = $object->amount;
        $this->_discountAmount = $object->discountAmount;
        $this->_totalAmount = $object->totalAmount;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $product = Product::findOne($this->product_id);

        if ($this->id !== '') {
            $object->id = (integer) $this->id;
        }
        $object->product_id = $product === null ? null : (integer) $this->product_id;
        $object->name = $this->name;
        $object->count = (integer) $this->count;
        $object->price = (float) $this->price;
        $object->discount = $this->discount === '' ? null : (integer) $this->discount;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'count' => Yii::t('catalog', 'Count'),
            'price' => Yii::t('catalog', 'Price'),
            'amount' => Yii::t('catalog', 'Amount'),
            'discount' => Yii::t('catalog', 'Discount'),
            'discountAmount' => Yii::t('catalog', 'Discount amount'),
            'totalAmount' => Yii::t('catalog', 'Total'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'product_id'], 'integer'],
            ['name', 'string'],
            ['count', 'integer', 'min' => 1],
            ['price', 'double', 'min' => 0],
            ['discount', 'integer', 'min' => 0, 'max' => 100],
            [['name', 'count', 'price'], 'required'],
        ]);
    }

    /**
     * Amount getter
     * @return float
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Discount amount getter
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->_discountAmount;
    }

    /**
     * Total amount getter
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->_totalAmount;
    }

}
