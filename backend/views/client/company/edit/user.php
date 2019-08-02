<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\User;
use common\models\UserServiceActive;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var View $this */
/** @var null|integer $id Company ID */
/** @var User $user */
/** @var array $languages */
/** @var UserServiceActive $lastActiveService */
/** @var integer $comments */
/** @var ActiveForm $form */

?>
<div id="company-user-information">
    <?php $form = ActiveForm::begin([
        'id' => 'edit-company-user-form',
        'action' => ['client/edit-company-user', 'id' => $user->id],
    ]); ?>
        <div id="A-C-134" class="text-right user-edit-id">
            <?php echo Yii::t('element', 'A-C-134', ['id' => $user->id]); ?>
        </div>

        <?php echo $form->field($user, 'email', [
            'inputOptions' => [
                'id' => 'A-C-133',
                'class' => 'form-control',
            ],
            'labelOptions' => [
                'id' => 'A-C-132',
                'class' => 'control-label',
            ],
        ])->label(Yii::t('element', 'A-C-132')); ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'name', [
                    'inputOptions' => [
                        'id' => 'A-C-136a',
                        'class' => 'form-control',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-135a',
                        'class' => 'control-label',
                    ],
                ])->label(Yii::t('element', 'A-C-135a')); ?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'surname', [
                    'inputOptions' => [
                        'id' => 'A-C-136b',
                        'class' => 'form-control',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-135b',
                        'class' => 'control-label',
                    ],
                ])->label(Yii::t('element', 'A-C-135b')); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'phone')->widget(PhoneInput::className(), [
                    'defaultOptions' => [
                        'id' => 'A-C-138',
                        'class' => 'form-control',
                    ],
                    'jsOptions' => [
                        // NOTE: there is no such country as 'en' therefore it is changed to 'gb'
                        'initialCountry' => Yii::$app->language == 'en' ? 'gb' : Yii::$app->language,
                        'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) to +370 612 3456
                    ],
                ])->label(Yii::t('element', 'A-C-137'), [
                    'id' => 'A-C-137',
                    'class' => 'control-label',
                ]); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'language', [
                    'options' => [
                        'id' => 'A-C-140',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-139',
                        'class' => 'control-label',
                    ],
                ])->widget(Select2::className(), [
                    'data' => $languages,
                    'options' => [
                        'id' => 'edit-user-languages',
                        'multiple' => true,
                    ],
                    'showToggleAll' => false,
                    'pluginOptions' => [
                        'escapeMarkup' => new JsExpression('function (m) {return m;}'), // NOTE: escapes HTML encoding
                    ],
                ])->label(Yii::t('element', 'A-C-139')); ?>
            </div>
        </div>

        <?php echo $form->field($user, 'password', [
            'inputOptions' => [
                'id' => 'A-C-142',
                'class' => 'form-control'
            ],
            'labelOptions' => [
                'id' => 'A-C-141',
                'class' => 'control-label',
            ],
        ])->label(Yii::t('element', 'A-C-141'))->passwordInput(); ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'blocked_until', [
                    'labelOptions' => [
                        'id' => 'A-C-143',
                        'class' => 'control-label',
                    ],
                ])->widget(DatePicker::className(), [
                    'options' => [
                        'id' => 'A-C-144',
                        'class' => 'form-control',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', 'A-C-143')); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 select">
                
                <?php echo $form->field($user, 'archive', [
                    'inputOptions' => [
                        'id' => 'A-C-146',
                        'class' => 'form-control',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-145',
                        'class' => 'control-label',
                    ],
                ])->dropDownList(User::getTranslatedArchives())->label(Yii::t('element', 'A-C-145')); ?>
            </div>
			<div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 select">
			<span class="select-arrow"><i class="fa fa-caret-down"></i></span>
			<?php
				if (!is_null($user->came_from_id)) {
					echo $form->field($user, 'came_from_id', [
						'inputOptions' => [
							'id' => 'A-C-142',
							'class' => 'form-control'
						],
						'labelOptions' => [
							'id' => 'A-C-141',
							'class' => 'control-label',
						],
					])->dropDownList($user->getTranslatedChoices(), [
								'prompt' => Yii::t('text', 'CAME_FROM_ID_DROP_DOWN_LIST_PROMPT'),
							])->label(Yii::t('text', 'ADMIN_EDIT_ACCOUNT_CAME_FROM_LABEL')); 
				}
			?>
			</div>
        </div>

        <div id="A-C-147" class="information-text-row">
            <?php echo Yii::t('element', 'A-C-147', [
                'value' => $user->convertLastLoginToString(),
            ]); ?>
        </div>

        <div id="A-C-149" class="information-text-row">
            <?php echo Yii::t('element', 'A-C-149', [
                'value' => is_null($lastActiveService) ? Yii::t('yii', '(not set)') : date('Y-m-d', $lastActiveService->end_date),
            ]); ?>
        </div>

        <div class="text-center user-edit-action-btn-row">
            <button id="A-C-153" class="primary-button user-edit-save-btn" type="submit">
                <?php echo Icon::show('floppy-o', [], Icon::FA) . Yii::t('element', 'A-C-153'); ?>
            </button>

            <a href="<?php echo Url::to([
                'site/login-to-user',
                'lang' => Yii::$app->language,
                'id' => $user->id,
            ]); ?>"
               id="A-C-152"
               class="secondary-button btn-link"
               target="_blank"
               data-pjax="0"
            >
                <?php echo Yii::t('element', 'A-C-152'); ?>
            </a>
        </div>

    <?php ActiveForm::end(); ?>

    <div class="text-right comments-container">
        <button id="A-C-151" class="secondary-button" onclick="addComment(event, <?php echo $id; ?>, 0);">
            <?php echo Icon::show('pencil-square-o', [], Icon::FA) . Yii::t('element', 'A-C-151'); ?>
        </button>

        <a href="<?php echo Url::to(['client/show-company-comments', 'lang' => Yii::$app->language, 'id' => $id]); ?>"
           id="A-C-151a"
           class="comments-link"
           target="_blank"
           data-pjax="0"
        >
            <?php echo Yii::t('element', 'A-C-151a', [
                'comments' => $comments,
            ]); ?>
        </a>
    </div>
    
    <?php Pjax::begin(['id' => 'company-comment-pjax-' . $id]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
$this->registerJs(
    'var actionRenderCompanyCommentForm = "' . Url::to([
        'client/render-company-comment-form',
        'lang' => Yii::$app->language,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/comment.js', ['depends' => [JqueryAsset::className()]]);