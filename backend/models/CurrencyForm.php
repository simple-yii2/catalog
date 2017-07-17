<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Currency;

/**
 * Editing form
 */
class CurrencyForm extends Model
{

	/**
	 * @var string name
	 */
	public $name;

	/**
	 * @var string Code
	 */
	public $code;

	/**
	 * @var float Rate
	 */
	public $rate;

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $suffix;

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
		parent::__construct(array_merge([
			'name' => $object->name,
			'code' => $object->code,
			'rate' => $object->rate,
			'prefix' => $object->prefix,
			'suffix' => $object->suffix,
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
			'prefix' => Yii::t('catalog', 'Prefix'),
			'suffix' => Yii::t('catalog', 'Suffix'),
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
			[['name', 'code', 'rate'], 'required'],
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
		$object->prefix = $this->prefix;
		$object->suffix = $this->suffix;

		if (!$object->save(false))
			return false;

		return true;
	}

}
