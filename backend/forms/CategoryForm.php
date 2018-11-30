<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\CategoryProperty;

class CategoryForm extends Form
{

    /**
     * @var boolean Active
     */
    public $active;

    /**
     * @var string Title
     */
    public $title;

    /**
     * @inheritdoc
     */
    public function forms()
    {
        return [
            ['properties', Form::HAS_MANY, CategoryPropertyForm::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'active' => Yii::t('catalog', 'Active'),
            'title' => Yii::t('catalog', 'Title'),
            'properties' => Yii::t('catalog', 'Properties'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['active', 'boolean'],
            ['title', 'string', 'max' => 100],
            ['title', 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->active = $object->active == 0 ? '0' : '1';
        $this->title = $object->title;
        $this->properties = array_merge($object->getParentProperties(), $object->properties);
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->active = $this->active == 0 ? false : true;
        $object->title = $this->title;
        $object->properties = $this->properties;
    }

}
