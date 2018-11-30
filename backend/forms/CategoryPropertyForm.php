<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use cms\catalog\models\CategoryProperty;

class CategoryPropertyForm extends Form
{

    /**
     * @inheritdoc
     */
    public $formName = 'CategoryForm[properties][]';

    /**
     * @var integer|null
     */
    public $id;

    /**
     * @var string Title
     */
    public $name;

    /**
     * @var integer Type
     */
    public $type;

    /**
     * @var string Measure unit
     */
    public $unit;

    /**
     * @var boolean Search by property enabled
     */
    public $search;

    /**
     * @var string[] Values
     */
    private $_values = [];

    /**
     * @var bool
     */
    private $_readOnly;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['values']);
    }

    /**
     * Read-only getter
     * @return boolean
     */
    public function getReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Getter for values
     * @return string[]
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Setter for values
     * @param string[] $value 
     * @return void
     */
    public function setValues($value)
    {
        $this->_values = is_array($value) ? $value : [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Title'),
            'type' => Yii::t('catalog', 'Type'),
            'values' => Yii::t('catalog', 'Values'),
            'unit' => Yii::t('catalog', 'Unit'),
            'search' => Yii::t('catalog', 'Search'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            ['name', 'string', 'max' => 50],
            ['type', 'in', 'range' => CategoryProperty::getTypes()],
            ['values', 'safe'],
            ['unit', 'string', 'max' => 10],
            ['search', 'boolean'],
            ['name', 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->id = $object->id;
        $this->name = $object->name;
        $this->type = $object->type;
        $this->values = $object->values;
        $this->unit = $object->unit;
        $this->search = $object->search;
        $this->_readOnly = $object->readOnly;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->name = $this->name;
        $object->type = $this->type;
        $object->values = $this->values;
        $object->unit = $this->unit;
        $object->search = $this->search;

        $object->makeAlias();
    }

}
