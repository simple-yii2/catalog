<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;

class CurrencyForm extends Form
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $code;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var integer
     */
    public $precision;

    /**
     * @var string
     */
    public $prefix;

    /**
     * @var string
     */
    public $suffix;

    /**
     * @var boolean
     */
    public $default;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'code' => Yii::t('catalog', 'Code'),
            'rate' => Yii::t('catalog', 'Rate'),
            'precision' => Yii::t('catalog', 'Precision'),
            'prefix' => Yii::t('catalog', 'Prefix'),
            'suffix' => Yii::t('catalog', 'Suffix'),
            'default' => Yii::t('catalog', 'Default'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['name', 'string', 'max' => 100],
            [['code', 'prefix', 'suffix'], 'string', 'max' => 10],
            ['rate', 'double', 'min' => 0.01],
            ['precision', 'in', 'range' => [0, 1, 2]],
            ['default', 'boolean'],
            [['name', 'code', 'rate', 'precision'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->name = $object->name;
        $this->code = $object->code;
        $this->rate = $object->rate;
        $this->precision = $object->precision;
        $this->prefix = $object->prefix;
        $this->suffix = $object->suffix;
        $this->default = $object->default == 0 ? '0' : '1';
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->name = $this->name;
        $object->code = $this->code;
        $object->rate = empty($this->rate) ? null : (float) $this->rate;
        $object->precision = (integer) $this->precision;
        $object->prefix = $this->prefix;
        $object->suffix = $this->suffix;
        $object->default = $this->default == 0 ? false : true;
    }

}
