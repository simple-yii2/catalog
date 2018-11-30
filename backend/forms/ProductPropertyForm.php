<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;

class ProductPropertyForm extends Form
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $property_id;

    /**
     * @var string Name
     */
    public $name;

    /**
     * @var string[] Enum values
     */
    public $values;

    /**
     * @var string|array Value
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', function() {
                if (!$this->_template->validateValue($this->value)) {
                    $this->addError('value', Yii::t('catalog', 'The value is incorrect.'));
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->id = $object->id;
        $this->property_id = $object->property_id;
        $this->name = $object->name;
        $this->values = $object->values;
        $this->value = $object->value;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->property_id = $this->_template->id;
        $object->value = $this->_template->formatValue($this->value);
    }

    // /**
    //  * Save object using model attributes
    //  * @param Product $product 
    //  * @param boolean $runValidation 
    //  * @return boolean
    //  */
    // public function save(Product $product, $runValidation = true)
    // {
    //     if ($runValidation && !$this->validate()) {
    //         return false;
    //     }

    //     $object = $this->_object;

    //     $object->product_id = $product->id;
    //     $object->property_id = $this->_template->id;
    //     $object->value = $this->_template->formatValue($this->value);

    //     if (!$object->save(false)) {
    //         return false;
    //     }

    //     return true;
    // }

}
