<?php

use common\models\FaqFeedback;
use frontend\controllers\SettingsController;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var FaqFeedback $model */

?>

<div id="site-help" class="site-help">
    <h1 id="DUK-C-1">
        <?php echo Yii::t('element', 'DUK-C-1'); ?>
    </h1>
    
    <?php echo Html::input('text', 'DUK-C-2', null, [
        'id' => 'DUK-C-2',
        'class' => 'search form-control',
        'placeholder' => Yii::t('app', 'INPUT_PLACEHOLDER_HELP_SEARCH'),
    ]); ?>
    
    <div id="DUK-C-3" class="faq-container">
        <!--  AUTOMOBILIŲ PERVEŽIMAS  -->

        <div class="faq-title">
            <h3><?php echo Yii::t('duk', 'BLOCK_TITLE_CAR_TRANSPORT'); ?></h3>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#want-transport-car">
                <?php echo Yii::t('duk', 'WANT_TRANSPORT_CAR'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="want-transport-car" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'WANT_TRANSPORT_CAR_ANSWER', [
                        'add ad' => Html::a(
                            Yii::t('duk', 'ADD_AD'),
                            Url::to(['load/announce', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <span id="DUK-AP-1">
                                <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                            </span>
                        </div>
                        
                        <div class="feedback-buttons">
                            <button type="button"
                                    id="DUK-AP-2"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#want-transport-car"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            
                            <button type="button"
                                    id="DUK-AP-3"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="WANT_TRANSPORT_CAR"
                                    data-id="want-transport-car"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#transportation-cost">
                <?php echo Yii::t('duk', 'TRANSPORTATION_COST'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="transportation-cost" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'TRANSPORTATION_COST_ANSWER'); ?>
                    
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#transportation-cost"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="TRANSPORTATION_COST"
                                    data-id="transportation-cost"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  APMOKĖJIMAS UŽ AUTO-LOADS  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_PAY_FOR_AUTO-LOADS'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#services-cost">
                <?php echo Yii::t('duk', 'SERVICES_COST'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="services-cost" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'SERVICES_COST_ANSWER', [
                        'fees' => Html::a(
                            Yii::t('duk', 'FEES'),
                            Url::to(['subscription/index', 'lang' => Yii::$app->language])
                        )
                    ]); ?>
                    
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#services-cost"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="SERVICES_COST"
                                    data-id="services-cost"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#where-pay-for-services">
                <?php echo Yii::t('duk', 'WHERE_PAY_FOR_SERVICES'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="where-pay-for-services" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'WHERE_PAY_FOR_SERVICES_ANSWERS', [
                        'pay' => Html::a(
                            Yii::t('duk', 'PAY'),
                            Url::to(['subscription/index', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>

                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>

                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#where-pay-for-services"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="WHERE_PAY_FOR_SERVICES"
                                    data-id="where-pay-for-services"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#when-use-services">
                <?php echo Yii::t('duk', 'WHEN_USE_SERVICES'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="when-use-services" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'WHEN_USE_SERVICES_ANSWER'); ?>

                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#when-use-services"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="WHEN_USE_SERVICES"
                                    data-id="when-use-services"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#cant-use-service-after-pay">
                <?php echo Yii::t('duk', 'CANT_USE_SERVICE_AFTER_PAY'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="cant-use-service-after-pay" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'CANT_USE_SERVICES_AFTER_PAY_ANSWER', [
                        'contact' => Html::a(
                            Yii::t('duk', 'CONTACT'),
                            Url::to(['site/imprint', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#cant-use-service-after-pay"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="CANT_USE_SERVICE_AFTER_PAY"
                                    data-id="cant-use-service-after-pay"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#membership-extension">
                <?php echo Yii::t('duk', 'MEMBERSHIP_EXTENSION'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="membership-extension" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'MEMBERSHIP_EXTENSION_ANSWER', [
                        'extend' => Html::a(
                            Yii::t('duk', 'EXTEND'),
                            Url::to(['subscription/index', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#membership-extension"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="MEMBERSHIP_EXTENSION"
                                    data-id="membership-extension"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  DUOMENŲ KEITIMAS  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_CHANGING_DATA'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#change-company-data">
                <?php echo Yii::t('duk', 'CHANGE_COMPANY_DATA'); ?>
                
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="change-company-data" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'CHANGE_COMPANY_DATA_ANSWER', [
                        'change' => Html::a(
                            Yii::t('duk', 'CHANGE'),
                            Url::to([
                                'settings/index',
                                'lang' => Yii::$app->language,
                                'tab' => SettingsController::TAB_EDIT_COMPANY_DATA,
                            ])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#change-company-data"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="CHANGE_COMPANY_DATA"
                                    data-id="change-company-data"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#change-my-data">
                <?php echo Yii::t('duk', 'CHANGE_MY_DATA'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="change-my-data" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'CHANGE_MY_DATA_ANSWER', [
                        'change' => Html::a(
                            Yii::t('duk', 'CHANGE'),
                            Url::to(['settings/index', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#change-my-data"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="CHANGE_MY_DATA"
                                    data-id="change-my-data"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#delete-my-data">
                <?php echo Yii::t('duk', 'DELETE_MY_DATA'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="delete-my-data" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'DELETE_MY_DATA_ANSWER', [
                        'contact' => Html::a(
                            Yii::t('duk', 'CONTACT'),
                            Url::to(['site/imprint', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#delete-my-data"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="DELETE_MY_DATA"
                                    data-id="delete-my-data"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  PRISIJUNGIMAS PRIE AUTO-LOADS  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_LOGIN_TO_AUTO-LOADS'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#forgot-password">
                <?php echo Yii::t('duk', 'FORGOT_PASSWORD'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="forgot-password" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'FORGOT_PASSWORD_ANSWER', [
                        'help' => Html::a(
                            Yii::t('duk', 'HELP'),
                            Url::to([
                                'settings/index',
                                'lang' => Yii::$app->language,
                                'tab' => SettingsController::TAB_CHANGE_PASSWORD,
                            ])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#forgot-password"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="FORGOT_PASSWORD"
                                    data-id="forgot-password"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#forgot-login-name">
                <?php echo Yii::t('duk', 'FORGOT_LOGIN_NAME'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="forgot-login-name" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'FORGOT_LOGIN_NAME_ANSWER'); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#forgot-login-name"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="FORGOT_LOGIN_NAME"
                                    data-id="forgot-login-name"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#cannot-receive-temporary-password">
                <?php echo Yii::t('duk', 'CANNOT_RECEIVE_TEMPORARY_PASSWORD'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="cannot-receive-temporary-password" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'CANNOT_RECEIVE_TEMPORARY_PASSWORD_ANSWER'); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#cannot-receive-temporary-password"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="CANNOT_RECEIVE_TEMPORARY_PASSWORD"
                                    data-id="cannot-receive-temporary-password"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#cannot-connect-no-such-user">
                <?php echo Yii::t('duk', 'CANNOT_CONNECT_NO_SUCH_USER'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            
            <div id="cannot-connect-no-such-user" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'CANNOT_CONNECT_NO_SUCH_USER_ANSWER', [
                        'contact' => Html::a(
                            Yii::t('duk', 'CONTACT'),
                            Url::to(['site/imprint', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#cannot-connect-no-such-user"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="CANNOT_CONNECT_NO_SUCH_USER"
                                    data-id="cannot-connect-no-such-user"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  NEVEIKIA  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_NOT_WORKING'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#not-working-auto-loads">
                <?php echo Yii::t('duk', 'NOT_WORKING_AUTO-LOADS'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="not-working-auto-loads" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'NOT_WORKING_AUTO-LOADS_ANSWER'); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#not-working-auto-loads"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="NOT_WORKING_AUTO-LOADS"
                                    data-id="not-working-auto-loads"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  SKELBIMAI  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_ADVERTISEMENTS'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#how-announce-load">
                <?php echo Yii::t('duk', 'HOW_ANNOUNCE_LOAD'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="how-announce-load" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'HOW_ANNOUNCE_LOAD_ANSWER', [
                        'announce' => Html::a(
                            Yii::t('duk', 'ANNOUNCE'),
                            Url::to(['load/announce', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#how-announce-load"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="HOW_ANNOUNCE_LOAD"
                                    data-id="how-announce-load"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#how-edit-ad">
                <?php echo Yii::t('duk', 'HOW_EDIT_AD'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="how-edit-ad" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'HOW_EDIT_AD_ANSWER', [
                        'edit' => Html::a(Yii::t('duk', 'EDIT'), [
                            'my-announcement/index',
                            'lang' => Yii::$app->language,
                        ]),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#how-edit-ad"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="HOW_EDIT_AD"
                                    data-id="how-edit-ad"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#how-delete-ad">
                <?php echo Yii::t('duk', 'HOW_DELETE_AD'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="how-delete-ad" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'HOW_DELETE_AD_ANSWER', [
                        'delete' => Html::a(Yii::t('duk', 'DELETE'), [
                            'my-announcement/index',
                            'lang' => Yii::$app->language,
                        ]),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#how-delete-ad"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="HOW_DELETE_AD"
                                    data-id="how-delete-ad"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  REGISTRACIJA  -->

        <div class="faq-title">
            <h4><?php echo Yii::t('duk', 'BLOCK_TITLE_REGISTRATION'); ?></h4>
        </div>

        <div class="faq-item">
            <div class="faq-question" data-toggle="collapse" data-target="#how-register-to-auto-loads">
                <?php echo Yii::t('duk', 'HOW_REGISTER_TO_AUTO-LOADS'); ?>
                <span class="faq-question-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </div>
            <div id="how-register-to-auto-loads" class="faq-answer collapse">
                <div class="faq-answer-content">
                    <?php echo Yii::t('duk', 'HOW_REGISTER_TO_AUTO-LOADS_ANSWER', [
                        'registration' => Html::a(
                            Yii::t('duk', 'REGISTRATION'),
                            Url::to(['site/sign-up', 'lang' => Yii::$app->language])
                        ),
                    ]); ?>
                    <div class="feedback-container">
                        <div class="feedback-question">
                            <?php echo Yii::t('element', 'DUK-AP-1'); ?>
                        </div>
                        <div class="feedback-buttons">
                            <button type="button"
                                    class="success-btn feedback-btn"
                                    data-toggle="collapse"
                                    data-target="#how-register-to-auto-loads"
                            >
                                <i class="icon-check"></i>
                                <?php echo Yii::t('element', 'DUK-AP-2'); ?>
                            </button>
                            <button type="button"
                                    class="danger-btn feedback-button-no feedback-btn"
                                    data-placeholder="HOW_REGISTER_TO_AUTO-LOADS"
                                    data-id="how-register-to-auto-loads"
                            >
                                <i class="icon-cross"></i>
                                <?php echo Yii::t('element', 'DUK-AP-3'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  ATSILIEPIMŲ FORMA  -->
    
    <div class="feedback-form-container hidden">
        <?php $form = ActiveForm::begin([
            'id' => 'faq-feedback-form',
            'action' => ['site/faq-feedback', 'lang' => Yii::$app->language],
        ]); ?>

            <span id="DUK-AP-3a" class="feedback-form-title">
                <?php echo Yii::t('element', 'DUK-AP-3a'); ?>
            </span>

            <span class="required-fields-text">
                <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
            </span>

            <?php echo $form->field($model, 'email', [
                'inputOptions' => [
                    'id' => 'DUK-AP-3c',
                ],
            ]); ?>

            <?php echo $form->field($model, 'comment', [
                'inputOptions' => [
                    'id' => 'DUK-AP-3e',
                ],
            ])->textarea(['rows' => 3]); ?>

            <?php echo Html::submitButton(Icon::show('paper-plane', '', Icon::FA) . Yii::t('element', 'DUK-AP-3f'), [
                'id' => 'DUK-AP-3f',
                'class' => 'primary-button',
            ]); ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs(
    'var defaultAction = "' . Url::to(['site/faq-feedback', 'lang' => Yii::$app->language, 'question' => '']) . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/list.min.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/site/footer/help.js', ['depends' => [JqueryAsset::className()]]);