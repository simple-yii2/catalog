<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;

use helpers\Translit;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\CategoryProperty;

/**
 * Category property form
 */
class CategoryPropertyForm extends Model
{

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
     * @var CategoryProperty
     */
    private $_object;

    /**
     * @inheritdoc
     * @param CategoryProperty|null $object 
     */
    public function __construct(CategoryProperty $object = null, $config = [])
    {
        if ($object === null) {
            $object = new CategoryProperty;
        }

        $this->_object = $object;

        //attributes
        parent::__construct(array_replace([
            'name' => $object->name,
            'type' => $object->type,
            'values' => $object->values,
            'unit' => $object->unit,
            'search' => $object->search,
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['values']);
    }

    /**
     * Id getter
     * @return integer|null
     */
    public function getId()
    {
        return $this->_object->id;
    }

    /**
     * Read-only getter
     * @return boolean
     */
    public function getReadOnly()
    {
        return $this->_object->readOnly;
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
        if (is_array($value)) {
            $this->_values = $value;
        } else {
            $this->_values = [];
        }
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
        return [
            ['name', 'string', 'max' => 50],
            ['type', 'in', 'range' => CategoryProperty::getTypes()],
            ['values', 'safe'],
            ['unit', 'string', 'max' => 10],
            ['search', 'boolean'],
            ['name', 'required'],
        ];
    }

    /**
     * Save
     * @param Category $category 
     * @param boolean $runValidation 
     * @return boolean
     */
    public function save(Category $category, $runValidation = true)
    {
        if ($this->_object->readOnly) {
            return false;
        }

        if ($runValidation && !$this->validate()) {
            return false;
        }

        $object = $this->_object;

        $object->category_id = $category->id;
        $object->name = $this->name;
        $object->type = $this->type;
        $object->values = $this->values;
        $object->unit = $this->unit;
        $object->search = $this->search;

        $object->makeAlias();

        if (!$object->save(false)) {
            return false;
        }

        return true;
    }

}
