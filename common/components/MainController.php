<?php

namespace common\components;

use common\components\audit\FailedAction;
use common\components\audit\Log;
use common\models\Seo;
use common\models\UserServiceActive;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Class MainController
 *
 * @package common\components
 */
class MainController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (Yii::$app->session->has('timezone')) {
            Yii::$app->timeZone = Yii::$app->session->get('timezone');
        }

        if (!Yii::$app->session->has('register_referer') && isset($_SERVER['HTTP_REFERER'])) {
            Yii::$app->session['register_referer'] = $_SERVER['HTTP_REFERER'];
        }

        if (!Yii::$app->session->has(UserServiceActive::SHOW_SUBSCRIPTION_ALERT)) {
            UserServiceActive::checkSubscriptionAlertVisibility();
        }

        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isBlocked()) {
            Yii::$app->user->logout();

            return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
        }

        $this->redirectToSubdomain();

        $this->setLanguageByDomain();

        $this->registerSeo();

        $result = parent::beforeAction($action);
        if (Yii::$app->getUser()->getIsGuest() && !$result) {
            Yii::$app->session['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [])
    {
        if (!Yii::$app->user->isGuest && Yii::$app->session->hasFlash('error')) {
            $userId = Yii::$app->user->id;
            $message = Yii::$app->session->getFlash('error');
            Log::user(FailedAction::ACTION, FailedAction::PLACEHOLDER_USER_GOT_ERROR_MESSAGE, $message, $userId);
        }

        if (!Yii::$app->user->isGuest && $view === 'error' && isset($params['message'])) {
            $userId = Yii::$app->user->id;
            $message = $params['message'];
            Log::user(FailedAction::ACTION, FailedAction::PLACEHOLDER_USER_GOT_ERROR_MESSAGE, $message, $userId);
        }

        return parent::render($view, $params);
    }

    /**
     * Identifies website language by website domain extension and redirects user to corresponding address
     *
     * For example, if hostname is auto-loads.ru,
     * then user will be redirected to same page (auto-loads.ru),
     * but website language will be russian.
     */
    private function setLanguageByDomain()
    {
        $host = Yii::$app->request->hostName;
        $subdomains = ['lt', 'en', 'pl', 'ru', 'de', 'es', 'ro', 'md'];
        $subdomainLang = explode('.', $host)[0];

        if (in_array($subdomainLang, $subdomains, true)) {
            $language = $subdomainLang;
        } else {
            $language = 'en';
        }

        Languages::setLanguage($language);
    }

    private function redirectToSubdomain()
    {
        if (preg_match('/^(\w{2})\.([\w\.-]+)\.(\w+)$/', Yii::$app->request->hostName, $matches)) {
            $cookie = new Cookie([
                'name'   => 'currentLanguage',
                'value'  => $matches[1],
                'expire' => time() + 86400 * 365,
                'domain' => '.' . Yii::$app->params['domain']
            ]);
            Yii::$app->response->cookies->add($cookie);
        } else {
            $language = Yii::$app->request->cookies->getValue('currentLanguage');

            if (!Languages::validateLanguage($language)) {
                $language = 'en';
            }

            $this->redirect(Url::to([
                Yii::$app->requestedRoute,
                'lang' => $language,
            ]));
        }
    }

    /**
     *
     */
    public function registerSeo()
    {
        $seoDefaults = [
            'keywords' => Yii::t('seo', 'KEYWORDS_META_TAG_CONTENT'),
            'description' => Yii::t('seo', 'DESCRIPTION_META_TAG_CONTENT')
        ];
        $route = Yii::$app->requestedRoute;
        if (!$route) {
            $route = 'site/index';
        }
        $seoModel = Seo::find()->where(['domain' => Yii::$app->language, 'route' => $route])->one();
        if ($seoModel instanceof Seo) {
            foreach ($seoModel->getAttributes(['title', 'keywords', 'description']) as $k => $value) {
                if (trim($value)) {
                    if ($k === 'title') {
                        $this->view->title = $value;
                    } else {
                        $seoDefaults[$k] = $value;
                    }
                }
            }
        }
        foreach ($seoDefaults as $name => $content) {
            $this->view->registerMetaTag(['name' => $name, 'content' => $content]);
        }
    }
}
