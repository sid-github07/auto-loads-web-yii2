<?php

use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $serialNumber integer */
/* @var $question string */
/* @var $companyName string */
/* @var $comment string */
?>

<div width="100%"
     height="100%"
     style="background: #f0f0f0; font-family: 'Verdana'; height: 100%; margin: 0 auto; padding: 0; width: 100%;
">
    <table cellpadding="0"
           cellspacing="0"
           border="0"
           height="100%"
           width="100%"
           style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; background: #f0f0f0;"
    >
        <tbody>
            <tr>
                <td>
                    <div style="margin-top: auto; margin-right: auto; margin-bottom: auto; margin-left: auto; max-width: 600px;">
                        <table width="100%" style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                            <tbody>
                                <tr style="height: 90px;">
                                    <td style="text-align: center;">
                                        <img src="<?php echo Url::to('@web/images/logo_200.png', true); ?>"
                                             alt="<?php echo Yii::t('element', 'HP-H-4'); ?>"
                                             width="200px"
                                             height="43px"
                                             style="width: 200px; height: 43px;"
                                        />
                                    </td>
                                </tr>
                                
                                <tr style="background: #ffffff;">
                                    <td style="
                                        border-radius: 4px;
                                        color: #242424;
                                        font-size: 15px;
                                        line-height: 1.5;
                                        padding: 24px;
                                    ">
                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_BODY_1'); ?>
                                    </td>
                                </tr>
                                
                                <tr style="background: #ffffff;">
                                    <td style="
                                        color: #242424;
                                        font-size: 15px;
                                        line-height: 1.5;
                                        padding: 0px 24px;
                                    ">
                                        <table width="100%" style="width: 100%; border-collapse: collapse; border-spacing: 0; border: 1px solid #dddddd;">
                                            <tbody>
                                                <tr>
                                                    <td style="
                                                        background-color: #278eda;
                                                        color: #ffffff;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #ffffff;
                                                        border-bottom: 1px solid #ffffff;
                                                        vertical-align: top;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_REQUEST_ID'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_REQUEST_NUMBER', [
                                                            'serialNumber' => $serialNumber,
                                                        ]); ?>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="
                                                        background-color: #278eda;
                                                        color: #ffffff;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #ffffff;
                                                        border-bottom: 1px solid #ffffff;
                                                        vertical-align: top;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_QUESTION_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_QUESTION', [
                                                            'question' => $question,
                                                        ]); ?>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="
                                                        background-color: #278eda;
                                                        color: #ffffff;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #ffffff;
                                                        border-bottom: 1px solid #ffffff;
                                                        vertical-align: top;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_COMMENT_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_COMMENT', [
                                                            'comment' => $comment,
                                                        ]); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>    
                                
                                <tr style="background: #ffffff;">
                                    <td style="
                                        border-radius: 4px;
                                        color: #242424;
                                        font-size: 15px;
                                        line-height: 1.5;
                                        padding: 32px 24px;
                                    ">
                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_BODY_2', [
                                            'companyName' => $companyName,
                                        ]); ?>
                                    </td>
                                </tr>
                                
                                <tr style="height: 80px;">
                                    <td style="text-align: center; font-size: 14px; color: #999999;">
                                        <?php echo Yii::t('mail', 'COMPANY_CONTACTS', [
                                            'companyName' => $companyName,
                                        ]); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>