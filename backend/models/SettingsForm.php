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
	private $_model;

	/**
	 * @inheritdoc
	 * @param Settings|null $model 
	 */
	public function __construct(Settings $model = null, $config = [])
	{
		if ($model === null)
			$model = new Settings;

		$this->_model = $model;

		//attributes
		$this->defaultCurrency_id = $model->defaultCurrency_id;
		$this->vendorImageWidth = $model->vendorImageWidth;
		$this->vendorImageHeight = $model->vendorImageHeight;
		$this->offerImageWidth = $model->offerImageWidth;
		$this->offerImageHeight = $model->offerImageHeight;

		parent::__construct($config);
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
	 * Saving model using model attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$model = $this->_model;

		$model->defaultCurrency_id = empty($this->defaultCurrency_id) ? null : (integer) $this->defaultCurrency_id;
		$model->vendorImageWidth = (integer) $this->vendorImageWidth;
		$model->vendorImageHeight = (integer) $this->vendorImageHeight;
		$model->offerImageWidth = (integer) $this->offerImageWidth;
		$model->offerImageHeight = (integer) $this->offerImageHeight;

		if (!$model->save(false))
			return false;

		return true;
	}

}
