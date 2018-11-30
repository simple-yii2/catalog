<?php

namespace cms\catalog\models;

use Yii;
use dkhlystov\db\ActiveRecord;
use helpers\Translit;

class CategoryProperty extends ActiveRecord
{

    const TYPE_BOOLEAN = 0;
    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_SELECT = 3;
    const TYPE_MULTIPLE = 4;

    /**
     * @var boolean properties from parent categories is read-only
     */
    public $readOnly = false;

    /**
     * @var array
     */
    private static $_typeNames;

    /**
     * @var string[] type names
     */
    private static $typeNames = [
        self::TYPE_BOOLEAN => 'Logical',
        self::TYPE_INTEGER => 'Integer',
        self::TYPE_FLOAT => 'Decimal',
        self::TYPE_SELECT => 'Select',
        self::TYPE_MULTIPLE => 'Multiple',
    ];

    /**
     * Getter for types
     * @return integer[]
     */
    public static function getTypes()
    {
        return array_keys(self::$typeNames);
    }

    /**
     * Getter for type names with translation
     * @return string[]
     */ 
    public static function getTypeNames()
    {
        if (self::$_typeNames !== null) {
            return self::$_typeNames;
        }

        return self::$_typeNames = array_map(function($name) {
            return Yii::t('catalog', $name);
        }, self::$typeNames);
    }

    /**
     * Getter for types which values needed
     * @return integer[]
     */
    public static function getTypesWithValues()
    {
        return [self::TYPE_SELECT, self::TYPE_MULTIPLE];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_category_property';
    }

    /**
     * Values getter
     * @return string[]
     */
    public function getValues()
    {
        $result = @unserialize($this->svalues);
        
        if (!is_array($result)) {
            $result = [];
        }

        return $result;
    }

    /**
     * Values setter
     * @param string[] $value 
     * @return void
     */
    public function setValues($value)
    {
        $this->svalues = serialize($value);
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        return parent::__construct(array_replace([
            'type' => self::TYPE_INTEGER,
        ], $config));
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
     * Value validation
     * @param string $value 
     * @return boolean
     */
    public function validateValue($value)
    {
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                return $this->validateBooleanValue($value);
                break;

            case self::TYPE_INTEGER:
                return $this->validateIntegerValue($value);
                break;

            case self::TYPE_FLOAT:
                return $this->validateFloatValue($value);
                break;

            case self::TYPE_SELECT:
                return $this->validateSelectValue($value);
                break;

            case self::TYPE_MULTIPLE:
                return $this->validateMultipleValue($value);
                break;
        }
    }

    /**
     * Boolean validation
     * @param string $value 
     * @return boolean
     */
    private function validateBooleanValue($value)
    {
        return $value == '0' || $value == '1';
    }

    /**
     * Integer validation
     * @param string $value 
     * @return boolean
     */
    private function validateIntegerValue($value)
    {
        return preg_match('/^\s*[+-]?\d+\s*$/', "$value");
    }

    /**
     * Decimal validation
     * @param string $value 
     * @return boolean
     */
    private function validateFloatValue($value)
    {
        return preg_match('/^\s*[+-]?\d+(?:\.\d+)?\s*$/', "$value");
    }

    /**
     * Select validation
     * @param string $value 
     * @return boolean
     */
    private function validateSelectValue($value)
    {
        return in_array($value, $this->getValues());
    }

    /**
     * Multiple validation
     * @param array $value 
     * @return boolean
     */
    private function validateMultipleValue($value)
    {
        $values = $this->getValues();

        if (!is_array($value)) {
            $value = empty($value) ? [] : [$value];
        }

        foreach ($value as $v) {
            if (!in_array($v, $values)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Value formatting for save
     * @param string $value 
     * @return string
     */
    public function formatValue($value)
    {
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                return $this->formatBoolean($value);
                break;

            case self::TYPE_INTEGER:
                return $this->formatInteger($value);
                break;

            case self::TYPE_FLOAT:
                return $this->formatFloat($value);
                break;

            case self::TYPE_SELECT:
                return $this->formatSelect($value);
                break;

            case self::TYPE_MULTIPLE:
                return $this->formatMultiple($value);
                break;
        }
    }

    /**
     * Boolean formatting
     * @param mixed $value 
     * @return string
     */
    private function formatBoolean($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value == 0 ? '0' : '1';
    }

    /**
     * Integer formatting
     * @param mixed $value 
     * @return string
     */
    private function formatInteger($value)
    {
        $value = (integer) trim($value);

        return (string) $value;
    }

    /**
     * Decimal formatting
     * @param mixed $value 
     * @return string
     */
    private function formatFloat($value)
    {
        $value = (float) trim($value);

        return (string) $value;
    }

    /**
     * Select formatting
     * @param mixed $value 
     * @return string
     */
    private function formatSelect($value)
    {
        return (string) $value;
    }

    /**
     * Multiple formatting
     * @param mixed $value 
     * @return string
     */
    private function formatMultiple($value, $separator = '/')
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return array_map(function ($v) {return (string) $v;}, $value);
    }

}
