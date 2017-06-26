<?php

namespace cms\catalog\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

use cms\catalog\backend\models\SettingsForm;
use cms\catalog\common\models\Settings;

class SettingsController extends Controller
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					['allow' => true, 'roles' => ['Catalog']],
				],
			],
		];
	}

	/**
	 * Form
	 * @return string
	 */
	public function actionIndex()
	{
		$form = new SettingsForm(Settings::find()->one());

		if ($form->load(Yii::$app->getRequest()->post()) && $form->save()) {
			Yii::$app->session->setFlash('success', Yii::t('catalog', 'Changes saved successfully.'));

			return $this->refresh();
		}

		return $this->render('index', [
			'form' => $form,
		]);
	}

}
