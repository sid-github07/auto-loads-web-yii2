<?php

use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */

$this->title = Yii::t('seo', 'TITLE_GUIDELINES');
?>

<div class="site-guidelines">
    <h1 id="TP-C-1">
        <?php echo Yii::t('element', 'TP-C-1'); ?>
    </h1>
    
    <div id="TP-C-2">
        <div class="question-wrapper">
            <a href="#guidelines-answer-1" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-1Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-1" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-1A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-2" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-2Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-2" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-2A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-3" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-3Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-3" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-3A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-4" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-4Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-4" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-4A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-5" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-5Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-5" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-5A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-6" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-6Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-6" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-6A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-7" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-7Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-7" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-7A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-8" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-8Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-8" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-8A'); ?>
                </div>
            </div>
        </div>
        
        <div class="question-wrapper">
            <a href="#guidelines-answer-9" data-toggle="collapse" class="guidelines-question">
                <?php echo Yii::t('element', 'TP-C-2-9Q'); ?>
                <i class="fa fa-caret-down"></i>
            </a>

            <div id="guidelines-answer-9" class="collapse guidelines-answer">
                <div class="guidelines-answer-content">
                    <?php echo Yii::t('element', 'TP-C-2-9A'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/footer/guidelines.js', ['depends' => [JqueryAsset::className()]]);