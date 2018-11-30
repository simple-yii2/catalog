<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Forms;

class ProductRelatedForm extends Forms
{

    /**
     * @var integer 
     */
    public $id;

    /**
     * @var integer
     */
    public $related_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'related_id'], 'integer'],
            [['id', 'related_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->id = $object->id;
        $this->related_id = $object->related_id;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->related_id = $this->related_id;
    }

}
