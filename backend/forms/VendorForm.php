<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use cms\catalog\common\models\Vendor;

/**
 * Editing form
 */
class VendorForm extends Model
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
     * @var Vendor
     */
    private $_object;

    /**
     * @inheritdoc
     * @param Vendor|null $object 
     */
    public function __construct(Vendor $object = null, $config = [])
    {
        if ($object === null)
            $object = new Vendor;

        $this->_object = $object;

        //file caching
        Yii::$app->storage->cacheObject($object);

        //attributes
        parent::__construct(array_merge([
            'name' => $object->name,
            'description' => $object->description,
            'url' => $object->url,
            'file' => $object->file,
            'thumb' => $object->thumb,
        ], $config));
    }

    /**
     * Object getter
     * @return Vendor
     */
    public function getObject()
    {
        return $this->_object;
    }

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
        return [
            ['name', 'string', 'max' => 100],
            ['description', 'string', 'max' => 3000],
            ['url', 'string', 'max' => 200],
            [['file', 'thumb'], 'string'],
            ['name', 'required'],
        ];
    }

    /**
     * Saving object using object attributes
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate())
            return false;

        $object = $this->_object;

        $object->name = $this->name;
        $object->description = $this->description;
        $object->url = $this->url;
        $object->file = $this->file;
        $object->thumb = $this->thumb;

        $object->makeAlias();

        //files
        Yii::$app->storage->storeObject($object);

        if (!$object->save(false))
            return false;

        return true;
    }

}
