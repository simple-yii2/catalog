<?php

namespace cms\catalog\models;

use dkhlystov\db\ActiveRecord;
use dkhlystov\storage\components\StoredInterface;
use helpers\Translit;

class Vendor extends ActiveRecord implements StoredInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_vendor';
    }

    /**
     * Making page alias from name
     * @return void
     */
    public function makeAlias()
    {
        $this->alias = Translit::t($this->name);
    }

    /**
     * Return files from attributes
     * @param array $attributes 
     * @return array
     */
    private function getFilesFromAttributes($attributes)
    {
        $files = [];

        if (!empty($attributes['file'])) {
            $files[] = $attributes['file'];
        }

        if (!empty($attributes['thumb'])) {
            $files[] = $attributes['thumb'];
        }

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function getOldFiles()
    {
        return $this->getFilesFromAttributes($this->getOldAttributes());
    }

    /**
     * @inheritdoc
     */
    public function getFiles()
    {
        return $this->getFilesFromAttributes($this->getAttributes());
    }

    /**
     * @inheritdoc
     */
    public function setFiles($files)
    {
        if (array_key_exists($this->file, $files)) {
            $this->file = $files[$this->file];
        }

        if (array_key_exists($this->thumb, $files)) {
            $this->thumb = $files[$this->thumb];
        }
    }

}
