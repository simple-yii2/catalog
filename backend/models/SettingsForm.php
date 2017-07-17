<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Settings;

/**
 * Editing form
 */
class SettingsForm extends Model
{

	/**
	 * @var integer Code
	 */
	public $defaultCurrency_id;

	/**
	 * @var integer price precision
	 */
	public $pricePrecision;

	/**
	 * @var Settings
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Settings|null $object 
	 */
	public function __construct(Settings $object = null, $config = [])
	{
		if ($object === null)
			$object = new Settings;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'defaultCurrency_id' => $object->defaultCurrency_id,
			'pricePrecision' => $object->pricePrecision,
		], $config));
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'defaultCurrency_id' => Yii::t('catalog', 'Default currency'),
			'pricePrecision' => Yii::t('catalog', 'Price precision'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['defaultCurrency_id', 'integer'],
			['pricePrecision', 'integer', 'min' => 0, 'max' => 2],
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

		$object->defaultCurrency_id = empty($this->defaultCurrency_id) ? null : (integer) $this->defaultCurrency_id;
		$object->pricePrecision = (integer) $this->pricePrecision;

		if (!$object->save(false))
			return false;

		return true;
	}

}
