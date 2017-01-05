<?php

namespace cms\catalog\backend;

use Yii;

use cms\catalog\common\models\Category;

class Module extends \yii\base\Module {

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->checkDatabase();
		self::addTranslation();
	}

	/**
	 * Database checking
	 * @return void
	 */
	protected function checkDatabase()
	{
		//schema
		$db = Yii::$app->db;
		$filename = dirname(__DIR__) . '/schema/' . $db->driverName . '.sql';
		$sql = explode(';', file_get_contents($filename));
		foreach ($sql as $s) {
			if (trim($s) !== '')
				$db->createCommand($s)->execute();
		}

		//rbac
		$auth = Yii::$app->getAuthManager();
		if ($auth->getRole('Catalog') === null) {
			//role
			$role = $auth->createRole('Catalog');
			$auth->add($role);
		}

		//data
		if (Category::find()->roots()->count() == 0) {
			$root = new Category(['title' => 'Root']);
			$root->makeRoot();
		}
	}

	/**
	 * Adding translation to i18n
	 * @return void
	 */
	protected static function addTranslation()
	{
		if (!isset(Yii::$app->i18n->translations['catalog'])) {
			Yii::$app->i18n->translations['catalog'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'sourceLanguage' => 'en-US',
				'basePath' => dirname(__DIR__) . '/messages',
			];
		}
	}

	/**
	 * Making main menu item of module
	 * @param string $base route base
	 * @return array
	 */
	public static function getMenu($base)
	{
		self::addTranslation();

		if (Yii::$app->user->can('Catalog')) {
			return [
				['label' => Yii::t('catalog', 'Catalog'), 'options' => ['class' => 'qwer'], 'items' => [
					['label' => Yii::t('catalog', 'Categories'), 'url' => ["$base/catalog/category/index"]],
				]],
			];
		}
		
		return [];
	}

}