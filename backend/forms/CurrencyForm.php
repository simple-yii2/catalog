<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Currency;

/**
 * Editing form
 */
class CurrencyForm extends Model
{

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $code;

	/**
	 * @var float
	 */
	public $rate;

	/**
	 * @var integer
	 */
	public $precision;

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $suffix;

	/**
	 * @var boolean
	 */
	public $default;

	/**
	 * @var Currency
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Currency|null $object 
	 */
	public function __construct(Currency $object = null, $config = [])
	{
		if ($object === null)
			$object = new Currency;

		$this->_object = $object;

		//attributes
		parent::__construct(array_replace([
			'name' => $object->name,
			'code' => $object->code,
			'rate' => $object->rate,
			'precision' => $object->precision,
			'prefix' => $object->prefix,
			'suffix' => $object->suffix,
			'default' => $object->default == 0 ? '0' : '1',
		], $config));
	}

	/**
	 * Object getter
	 * @return Currency
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
			'code' => Yii::t('catalog', 'Code'),
			'rate' => Yii::t('catalog', 'Rate'),
			'precision' => Yii::t('catalog', 'Precision'),
			'prefix' => Yii::t('catalog', 'Prefix'),
			'suffix' => Yii::t('catalog', 'Suffix'),
			'default' => Yii::t('catalog', 'Default'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'string', 'max' => 100],
			[['code', 'prefix', 'suffix'], 'string', 'max' => 10],
			['rate', 'double', 'min' => 0.01],
			['precision', 'in', 'range' => [0, 1, 2]],
			['default', 'boolean'],
			[['name', 'code', 'rate', 'precision'], 'required'],
		];
	}

	/**
	 * Save
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$object = $this->_object;

		$object->name = $this->name;
		$object->code = $this->code;
		$object->rate = empty($this->rate) ? null : (float) $this->rate;
		$object->precision = (integer) $this->precision;
		$object->prefix = $this->prefix;
		$object->suffix = $this->suffix;
		$object->default = $this->default == 0 ? false : true;

		$transaction = $object->db->beginTransaction();
		try {
			$success = $object->save(false);
			if ($success && $object->default) {
				$object->updateAll(['default' => false], ['<>', 'id', $object->id]);
			}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
		}

		return $success;
	}

}
