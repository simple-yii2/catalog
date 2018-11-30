<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;

class VendorForm extends Form
{

    /**
     * @var string Name
     */
    public $name;

    /**
     * @var string Description
     */
    public $description;

    /**
     * @var string Url
     */
    public $url;

    /**
     * @var string Image
     */
    public $file;

    /**
     * @var string Thumb
     */
    public $thumb;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'description' => Yii::t('catalog', 'Description'),
            'url' => Yii::t('catalog', 'Url'),
            'file' => Yii::t('catalog', 'Image'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['name', 'string', 'max' => 100],
            ['description', 'string', 'max' => 3000],
            ['url', 'string', 'max' => 200],
            [['file', 'thumb'], 'string'],
            ['name', 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        // File caching
        Yii::$app->storage->cacheObject($object);

        $this->name = $object->name;
        $this->description = $object->description;
        $this->url = $object->url;
        $this->file = $object->file;
        $this->thumb = $object->thumb;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->name = $this->name;
        $object->description = $this->description;
        $object->url = $this->url;
        $object->file = $this->file;
        $object->thumb = $this->thumb;

        $object->makeAlias();

        // Files
        Yii::$app->storage->storeObject($object);
    }

}
