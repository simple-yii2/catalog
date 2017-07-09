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
		], $config));
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'defaultCurrency_id' => Yii::t('catalog', 'Default currency'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['defaultCurrency_id', 'integer'],
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

		if (!$object->save(false))
			return false;

		return true;
	}

}
