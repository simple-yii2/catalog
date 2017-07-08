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
	 * @var integer Rate
	 */
	public $vendorImageWidth;

	/**
	 * @var integer Rate
	 */
	public $vendorImageHeight;

	/**
	 * @var integer Rate
	 */
	public $offerImageWidth;

	/**
	 * @var integer Rate
	 */
	public $offerImageHeight;

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
			'vendorImageWidth' => $object->vendorImageWidth,
			'vendorImageHeight' => $object->vendorImageHeight,
			'offerImageWidth' => $object->offerImageWidth,
			'offerImageHeight' => $object->offerImageHeight,
		], $config));
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'defaultCurrency_id' => Yii::t('catalog', 'Default currency'),
			'vendorImageWidth' => Yii::t('catalog', 'Vendor image width'),
			'vendorImageHeight' => Yii::t('catalog', 'Vendor image height'),
			'offerImageWidth' => Yii::t('catalog', 'Offer image width'),
			'offerImageHeight' => Yii::t('catalog', 'Offer image height'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['defaultCurrency_id', 'integer'],
			[['vendorImageWidth', 'vendorImageHeight', 'offerImageWidth', 'offerImageHeight'], 'integer', 'min' => 20],
			[['vendorImageWidth', 'vendorImageHeight', 'offerImageWidth', 'offerImageHeight'], 'required'],
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

		$object->defaultCurrency_id = empty($this->defaultCurrency_id) ? null : (integer) $this->defaultCurrency_id;
		$object->vendorImageWidth = (integer) $this->vendorImageWidth;
		$object->vendorImageHeight = (integer) $this->vendorImageHeight;
		$object->offerImageWidth = (integer) $this->offerImageWidth;
		$object->offerImageHeight = (integer) $this->offerImageHeight;

		if (!$object->save(false))
			return false;

		return true;
	}

}
