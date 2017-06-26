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
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['defaultCurrency_id', 'integer'],
			[['vendorImageWidth', 'vendorImageHeight'], 'integer', 'min' => 10],
			[['vendorImageWidth', 'vendorImageHeight'], 'required'],
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

		if (!$model->save(false))
			return false;

		return true;
	}

}
