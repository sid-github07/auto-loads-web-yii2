<?php

namespace backend\controllers;

use common\models\Seo;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\components\Languages;

/**
 * Class SeoController
 * @package backend\controllers
 */
class SeoController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'edit'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest && Yii::$app->admin->identity->isAdmin();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'edit' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * @param string $domain
     * @param string $route
     * @return string
     */
    public function actionEdit($route, $domain = null)
    {
        if (is_null($domain)) {
            $domain = Yii::$app->language;
        }

        if (Yii::$app->getRequest()->getIsPost()) {
            $this->saveSeoData();
            return $this->redirect(Yii::$app->request->url); // To avoid casual post duplicates
        }

        $seo = Seo::find()->where(['route' => $route, 'domain' => $domain])->one();
        if (!$seo instanceof Seo) {
            $seo = new Seo();
            $seo->setAttributes([
                'route' => $route,
                'page' => $route,
                'lang' => $domain
            ]);
        }

        $languages = array_map(function ($el) {
            preg_match('!\S+$!i', $el, $match);
            return isset($match[0]) ? $match[0] : $el;
        }, Languages::getLanguages());

        return $this->render('edit', [
            'domain' => $domain,
            'languages' => $languages,
            'seoModel' => $seo
        ]);
    }

    /**
     *
     */
    private function saveSeoData()
    {
        $request = Yii::$app->getRequest()->post('Seo');
        $seo = Seo::find()->where(['domain' => $request['domain'], 'page' => $request['page']])->one();
        if (!$seo instanceof Seo) {
            $seo = new Seo();
        }
        $seo->setAttributes($request);
        if (!$seo->save()) {
            if ($seo->hasErrors() && !empty($seo->getFirstErrors())) {
                return Yii::$app->session->setFlash('error', Yii::t('alert', array_values($seo->getFirstErrors())[0]));
            }
            return Yii::$app->session->setFlash('error', Yii::t('alert', 'UNKNOWN_ERROR_OCCURED'));
        }
        return Yii::$app->session->setFlash('success', Yii::t('alert', 'SUCCESSFULLY_SAVED'));
    }
}

