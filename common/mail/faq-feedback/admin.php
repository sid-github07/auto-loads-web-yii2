<?php

use yii\web\View;

/* @var $this View */
/* @var $serialNumber integer */
/* @var $question string */
/* @var $clientEmail string */
/* @var $isSolved string */
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
                                <tr style="height: 90px;"></tr>
                                
                                <tr style="background: #ffffff;">
                                    <td style="
                                        border-radius: 4px;
                                        color: #242424;
                                        font-size: 15px;
                                        line-height: 1.5;
                                        padding: 24px;
                                    ">
                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_FAQ_QUESTION'); ?>
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
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_REQUEST_ID'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_REQUEST_NUMBER', [
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
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_QUESTION_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_QUESTION', [
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
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_EMAIL_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_EMAIL', [
                                                            'clientEmail' => $clientEmail,
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
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_IS_SOLVED_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_IS_SOLVED', [
                                                            'isSolved' => $isSolved,
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
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_COMMENT_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_COMMENT', [
                                                            'comment' => $comment,
                                                        ]); ?>
                                                    </td>
                                                </tr>
                                                
                                                <tr style="height: 50px;"></tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr style="height: 80px;"></tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>