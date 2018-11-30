<?php

namespace cms\catalog\backend\forms;

use Yii;
use dkhlystov\forms\Form;

class ProductBarcodeForm extends Form
{

    /**
     * @inheritdoc
     */
    public $formName = 'ProductForm[barcodes][]';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string Barcode
     */
    public $barcode;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barcode' => Yii::t('catalog', 'Value'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            ['barcode', 'string', 'max' => 50],
            ['barcode', 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->id = $object->id;
        $this->barcode = $object->barcode;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $object->barcode = $this->barcode;
    }

}
