<?php

namespace cms\catalog\frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use cms\catalog\common\models\Category;
use cms\catalog\frontend\models\Product;
use cms\catalog\frontend\filters\ProductFilter;

class ProductController extends Controller
{

    /**
     * Product list
     * @param string|null $alias 
     * @return string
     */
    public function actionIndex($alias = null)
    {
        $category = Category::findByAlias($alias);
        if ($category === null) {
            $category = Category::find()->roots()->one();
        }

        $filterModel = new ProductFilter;
        $filterModel->category = $category;

        if ($filterModel->getQuery()->count() == 0) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->render('index', [
            'category' => $category,
            'filterModel' => $filterModel,
        ]);
    }

    /**
     * Product view
     * @param string $alias 
     * @return string
     */
    public function actionView($alias)
    {
        $model = Product::findByAlias($alias);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

}
