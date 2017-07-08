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
	 * @var string Code
	 */
	public $code;

	/**
	 * @var float Rate
	 */
	public $rate;

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
			'code' => $object->code,
			'rate' => $object->rate,
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
			'code' => Yii::t('catalog', 'Code'),
			'rate' => Yii::t('catalog', 'Rate'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['code', 'string', 'max' => 10],
			['rate', 'double', 'min' => 0.01],
			[['code', 'rate'], 'required'],
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

		$object->code = $this->code;
		$object->rate = empty($this->rate) ? null : (float) $this->rate;

		if (!$object->save(false))
			return false;

		return true;
	}

}
