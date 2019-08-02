<?php

use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var string $title */
/** @var integer $loadCarsSum */
/** @var integer $transportedLoadCarsSum */

?>
<?php if (!Yii::$app->user->isGuest): ?>
    <div class="site-index logged-in">
        <a href="<?php echo Yii::$app->homeUrl; ?>" class="index-logo-link">
            <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                 id="HP-C1-1"
                 class="index-site-logo"
                 alt="<?php echo Yii::t('element', 'HP-C1-1'); ?>"
            />
        </a>

        <div class="row">
            <div class="load-car-totals col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <a href="<?php echo Url::to(['/load/loads', 'lang' => Yii::$app->language]); ?>"
                   class="counter vehicle-number ready load-loads-link" data-toggle="tooltip"
                   title="<?php echo Yii::t('element', 'HP-C6-1a');?>">
                <?php echo $loadCarsSum; ?>
                </a><div class="load-car-totals-title"><?php echo Yii::t('element', 'total_ready_cars'); ?></div>
            </div>
            <div class="load-car-totals col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="counter vehicle-number transported"><?php echo $transportedLoadCarsSum; ?>
                </div><div class="load-car-totals-title"><?php echo Yii::t('element', 'total_transported_cars'); ?></div>
            </div>
        </div>
        
        <div class="action-buttons-container">
            <div class="row">
                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to(['load/search', 'lang' => Yii::$app->language]); ?>"
                       id="HP-C4-3"
                       class="action-button"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'HP-C4-3'); ?>
                        </span>
                    </a>
                </div>

                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to(['load/announce', 'lang' => Yii::$app->language]); ?>"
                       id="HP-C5-4"
                       class="action-button"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'HP-C5-4'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="action-buttons-container">
            <div class="row">
                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to([
                            'car-transporter-search/search-form',
                            'lang' => Yii::$app->language]); ?>"
                       id="C-T-66"
                       class="action-button-alt"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-search"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'C-T-66'); ?>
                        </span>
                    </a>
                </div>

                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to([
                        'car-transporter-announcement/announcement-form',
                        'lang' => Yii::$app->language,
                    ]); ?>"
                       id="C-T-25"
                       class="action-button-alt"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-bullhorn"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'C-T-25'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="site-index">
        <a href="<?php echo Yii::$app->homeUrl; ?>" class="index-logo-link">
            <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                id="HP-C1-1"
                class="index-site-logo"
                alt="<?php echo Yii::t('element', 'HP-C1-1'); ?>"
            />
        </a>

        <div class="row">
            <div class="load-car-totals col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <a href="<?php echo Url::to(['/load/loads', 'lang' => Yii::$app->language]); ?>"
                    class="counter vehicle-number ready load-loads-link" data-toggle="tooltip"
                    title="<?php echo Yii::t('element', 'HP-C6-1a');?>">
                <?php echo $loadCarsSum; ?>
                </a><div class="load-car-totals-title"><?php echo Yii::t('element', 'total_ready_cars'); ?></div>
            </div>
            <div class="load-car-totals col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="counter vehicle-number transported"><?php echo $transportedLoadCarsSum; ?>
                </div><div class="load-car-totals-title"><?php echo Yii::t('element', 'total_transported_cars'); ?></div>
            </div>
        </div>
        
        <div class="action-buttons-container">
            <div class="row">
                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to(['load/search', 'lang' => Yii::$app->language]); ?>"
                       id="HP-C4-2"
                       class="action-button"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'HP-C4-2'); ?>
                        </span>
                    </a>
                </div>

                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to(['load/announce', 'lang' => Yii::$app->language]); ?>"
                       id="HP-C5-3"
                       class="action-button"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                        </span>

                        <span id="HP-C5-3b" class="action-button-label">
                            <?php echo Yii::t('element', 'HP-C5-3'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="action-buttons-container">
            <div class="row">
                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to([
                            'car-transporter-search/search-form',
                            'lang' => Yii::$app->language]); ?>"
                       id="C-T-65"
                       class="action-button-alt"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-search"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'C-T-65'); ?>
                        </span>
                    </a>
                </div>

                <div class="action-button-wrapper col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a href="<?php echo Url::to([
                        'car-transporter-announcement/announcement-form',
                        'lang' => Yii::$app->language,
                    ]); ?>"
                       id="C-T-24"
                       class="action-button-alt"
                    >
                        <span class="action-button-icon">
                            <i class="fa fa-bullhorn"></i>
                        </span>

                        <span class="action-button-label">
                            <?php echo Yii::t('element', 'C-T-24'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$this->registerJsFile(Url::base() . '/dist/js/site/index.js', ['depends' => [JqueryAsset::className()]]);
