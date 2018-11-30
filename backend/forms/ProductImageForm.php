<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\ProductImage;

class ProductImageForm extends Form
{

    /**
     * @var integer|null
     */
    public $id;

    /**
     * @var string
     */
    public $file;

    /**
     * @var string
     */
    public $thumb;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            [['file', 'thumb'], 'string', 'max' => 200],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        Yii::$app->storage->cacheObject($object);

        $this->id = $object->id;
        $this->file = $object->file;
        $this->thumb = $object->thumb;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->file = $this->file;
        $object->thumb = $this->thumb;

        Yii::$app->storage->storeObject($object);
    }

}
