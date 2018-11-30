<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use cms\catalog\common\helpers\CurrencyHelper;

class OrderForm extends Form
{

    /**
     * @var integer
     */
    public $currency_id;

    /**
     * @var string
     */
    public $number;

    /**
     * @var datetime
     */
    public $issueDate;

    /**
     * @var datetime
     */
    public $paymentTerm;

    /**
     * @var integer
     */
    public $paymentState;

    /**
     * @var float
     */
    public $paidAmount;

    /**
     * @var string
     */
    public $customerEmail;

    /**
     * @var integer
     */
    public $discount;

    /**
     * @var string
     */
    public $comment;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->currency_id = CurrencyHelper::getApplicationCurrencyId();
        $this->issueDate = date('Y-m-d');
        $this->discount = 0;
    }

    /**
     * @inheritdoc
     */
    public function nestedForms()
    {
        return [
            ['customer', Form::HAS_ONE, OrderCustomerForm::className()],
            ['delivery', Form::HAS_ONE, OrderDeliveryForm::className()],
            ['products', Form::HAS_MANY, OrderProductForm::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'number' => Yii::t('catalog', 'Order number'),
            'issueDate' => Yii::t('catalog', 'Issue date'),
            'paymentTerm' => Yii::t('catalog', 'Payment term'),
            'paymentState' => Yii::t('catalog', 'Payment state'),
            'paidAmount' => Yii::t('catalog', 'Paid amount'),
            'currency_id' => Yii::t('catalog', 'Currency'),
            'discount' => Yii::t('catalog', 'Discount'),
            'comment' => Yii::t('catalog', 'Comment'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['number', 'string', 'max' => 20],
            [['issueDate', 'paymentTerm'], 'date', 'format' => 'yyyy-MM-dd'],
            ['discount', 'integer', 'min' => 0, 'max' => 100],
            ['comment', 'string', 'max' => 65535],
            [['number', 'issueDate', 'discount'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->currency_id = $object->currency_id;
        $this->number = $object->number;
        $this->issueDate = date('Y-m-d', strtotime($object->issueDate));
        $this->paymentTerm = $object->paymentTerm;
        $this->paymentState = $object->paymentState;
        $this->paidAmount = $object->paidAmount;
        $this->discount = $object->discount;
        $this->comment = $object->comment;
        $this->customer = $object->customer;
        $this->delivery = $object->delivery;
        $this->products = $object->products;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $currency =CurrencyHelper::getCurrency($this->currency_id);

        $object->currency_id = $currency === null ? null : (integer) $currency->id;
        $object->number = $this->number;
        $object->issueDate = $this->issueDate;
        $object->paymentTerm = $this->paymentTerm;
        $object->paymentState = (integer) $this->paymentState;
        $object->paidAmount = (float) $this->paidAmount;
        $object->discount = (integer) $this->discount;
        $object->comment = $this->comment;
        $object->customer = $this->customer;
        $object->delivery = $this->delivery;
        $object->products = $this->products;
    }

}
