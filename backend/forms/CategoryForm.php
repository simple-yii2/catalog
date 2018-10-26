<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\CategoryProperty;

class CategoryForm extends Model
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
     * @var CategoryPropertyForm[] Properties
     */
    private $_properties = [];

    /**
     * @var Category
     */
    private $_object;

    /**
     * @inheritdoc
     * @param Category $object 
     */
    public function __construct(Category $object = null, $config = [])
    {
        if ($object === null) {
            $object = new Category;
        }

        $this->_object = $object;

        //attributes
        parent::__construct(array_replace([
            'active' => $object->active == 0 ? '0' : '1',
            'title' => $object->title,
            'properties' => array_merge($object->getParentProperties(), $object->properties),
        ], $config));
    }

    /**
     * Properies getter
     * @return CategoryPropertyForm[]
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Properies setter
     * @param CategoryProperty[]|array[] $value Properies
     * @return void
     */
    public function setProperties($value)
    {
        $old = [];
        foreach ($this->_properties as $formModel) {
            if ($id = $formModel->getId()) {
                $old[$id] = $formModel;
            }
        }

        $this->_properties = [];

        //read only
        foreach ($old as $key => $item) {
            if ($item->readOnly) {
                $this->_properties[] = $item;
                unset($old[$key]);
            }
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $item) {
            if ($item instanceof CategoryProperty) {
                $object = $item;
                $id = $item->id;
                $attributes = $item->getAttributes();
            } else {
                $object = null;
                $id = ArrayHelper::getValue($item, 'id');
                $attributes = $item;
            }

            $formModel = array_key_exists($id, $old) ? $old[$id] : new CategoryPropertyForm($object);

            $formModel->setAttributes($attributes);
            $this->_properties[] = $formModel;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['properties']);
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
        return [
            ['active', 'boolean'],
            ['title', 'string', 'max' => 100],
            ['title', 'required'],
            ['properties', function($attribute, $params) {
                $hasError = false;
                foreach ($this->$attribute as $model) {
                    if (!$model->validate()) {
                        $hasError = true;
                    }
                }

                if ($hasError) {
                    $this->addError($attribute . '[]', 'Properties validation error.');
                }
            }],
        ];
    }

    /**
     * Object getter
     * @return Category
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Save
     * @param Category|null $parent 
     * @param boolean $runValidation 
     * @return boolean
     */
    public function save(Category $parent = null, $runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $object = $this->_object;

        //attributes
        $object->active = $this->active == 0 ? false : true;
        $object->title = $this->title;

        //save
        if ($object->getIsNewRecord()) {
            if (!$object->appendTo($parent, false)) {
                return false;
            }
        } else {
            if (!$object->save(false)) {
                return false;
            }
        }

        //update alias and path
        $object->updateAliasAndPath($parent);

        //update relations
        $old = [];
        foreach ($object->properties as $item) {
            $old[$item->id] = $item;
        };
        //insert/update
        foreach ($this->_properties as $item) {
            $item->save($object, false);
            unset($old[$item->id]);
        }
        //delete
        foreach ($old as $item) {
            $item->delete();
        }

        return true;
    }

}
