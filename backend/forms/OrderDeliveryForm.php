<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use cms\catalog\common\helpers\DeliveryHelper;
use cms\catalog\common\models\Store;

class OrderDeliveryForm extends Form
{

    /**
     * @var string
     */
    public $delivery;

    /**
     * @var float
     */
    public $price;

    /**
     * @var integer
     */
    public $days;

    /**
     * @var integer;
     */
    public $store_id;

    /**
     * @var string
     */
    public $serviceName;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $house;

    /**
     * @var string
     */
    public $apartment;

    /**
     * @var string
     */
    public $entrance;

    /**
     * @var string
     */
    public $entryphone;

    /**
     * @var integer
     */
    public $floor;

    /**
     * @var string
     */
    public $recipient;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $trackingCode;

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->delivery = DeliveryHelper::getDeliveryKey($object->delivery_class);
        $this->price = $object->price;
        $this->days = $object->days;
        $this->store_id = $object->store_id;
        $this->serviceName = $object->serviceName;
        $this->city = $object->city;
        $this->street = $object->street;
        $this->house = $object->house;
        $this->apartment = $object->apartment;
        $this->entrance = $object->entrance;
        $this->entryphone = $object->entryphone;
        $this->floor = $object->floor;
        $this->recipient = $object->recipient;
        $this->phone = $object->phone;
        $this->trackingCode = $object->trackingCode;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $deliveryClass = DeliveryHelper::getDeliveryClass($this->delivery);
        $store = Store::findOne($this->store_id);

        $object->delivery_class = $deliveryClass;
        $object->name = $deliveryClass === null ? '' : $deliveryClass::getName();
        $object->price = (float) $this->price;
        $object->days = (integer) $this->days;
        $object->availableFields = $deliveryClass === null ? [] : $deliveryClass::getAvailableFields();
        $object->store_id = $store === null ? null : (integer) $store->id;
        $object->serviceName = $this->serviceName;
        $object->city = $this->city;
        $object->street = $this->street;
        $object->house = $this->house;
        $object->apartment = $this->apartment;
        $object->entrance = $this->entrance;
        $object->entryphone = $this->entryphone;
        $object->floor = $this->floor;
        $object->recipient = $this->recipient;
        $object->phone = $this->phone;
        $object->trackingCode = $this->trackingCode;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery' => Yii::t('catalog', 'Delivery method'),
            'price' => Yii::t('catalog', 'Price'),
            'days' => Yii::t('catalog', 'Days'),
            'store_id' => Yii::t('catalog', 'Store'),
            'serviceName' => Yii::t('catalog', 'Service name'),
            'city' => Yii::t('catalog', 'City'),
            'street' => Yii::t('catalog', 'Street'),
            'house' => Yii::t('catalog', 'House'),
            'apartment' => Yii::t('catalog', 'Apartment'),
            'entrance' => Yii::t('catalog', 'Entrance'),
            'entryphone' => Yii::t('catalog', 'Entryphone'),
            'floor' => Yii::t('catalog', 'Floor'),
            'recipient' => Yii::t('catalog', 'Recipient'),
            'phone' => Yii::t('catalog', 'Phone'),
            'trackingCode' => Yii::t('catalog', 'Tracking code'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['delivery', 'string'],
            [['store_id'], 'integer'],
            ['price', 'double', 'min' => 0],
            ['days', 'integer', 'min' => 0],
            [['serviceName', 'city', 'street', 'entrance', 'recipient'], 'string', 'max' => 100],
            [['house', 'apartment', 'entryphone', 'floor'], 'string', 'max' => 10],
            [['phone', 'trackingCode'], 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/\+1\-\d{3}\-\d{3}\-\d{4}/'],
            [['delivery', 'price', 'days'], 'required'],
        ]);
    }

}
