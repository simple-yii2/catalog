<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Delivery;

class DeliveryForm extends Model
{

    /**
     * @var float
     */
    public $name;

    /**
     * @var integer
     */
    public $currency_id;

    /**
     * @var float
     */
    public $price;

    /**
     * @var integer
     */
    public $days;

    /**
     * @var Delivery
     */
    private $_object;

    /**
     * @inheritdoc
     * @param Delivery|null $object 
     */
    public function __construct(Delivery $object = null, $config = [])
    {
        if ($object === null) {
            $object = new Delivery;
        }

        $this->_object = $object;

        //attributes
        parent::__construct(array_replace([
            'name' => $object->name,
            'currency_id' => $object->currency_id,
            'price' => $object->price,
            'days' => $object->days,
        ], $config));
    }

    /**
     * Object getter
     * @return Delivery
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'currency_id' => Yii::t('catalog', 'Currency'),
            'price' => Yii::t('catalog', 'Price'),
            'days' => Yii::t('catalog', 'Days'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 100],
            ['currency_id', 'integer'],
            ['price', 'double', 'min' => 0],
            ['days', 'integer', 'min' => 0],
            [['name', 'price', 'days'], 'required'],
        ];
    }

    /**
     * Saving object using object attributes
     * @param boolean $runValidation 
     * @return boolean
     */
    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $currency = Currency::findOne($this->currency_id);

        $object = $this->_object;

        $object->name = $this->name;
        $object->currency_id = $currency === null ? null : $currency->id;
        $object->price = empty($this->price) ? null : (float) $this->price;
        $object->days = (integer) $this->days;

        if (!$object->save(false)) {
            return false;
        }

        return true;
    }

}
