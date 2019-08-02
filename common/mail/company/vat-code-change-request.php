<?php

/* @var $content string */
/* @var $userEmail string */
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
                                        color: #242424;
                                        font-size: 15px;
                                        line-height: 1.5;
                                        padding: 24px;
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
                                                        <?php echo Yii::t('mail', 'USER_REQUEST_EMAIL_CHANGE_EMAIL_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                    ">
                                                        <?php echo Yii::t('mail', 'USER_REQUEST_EMAIL_CHANGE_EMAIL', [
                                                            'userEmail' => $userEmail,
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
                                                        <?php echo Yii::t('mail', 'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT_NAME'); ?>
                                                    </td>
                                                    
                                                    <td style="
                                                        background-color: #fafafa;
                                                        color: #242424;
                                                        padding: 8px 24px;
                                                        font-size: 14px;
                                                        border-top: 1px solid #dddddd;
                                                        border-bottom: 1px solid #dddddd;
                                                    ">
                                                        <?php echo Yii::t('mail', 'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT', [
                                                            'content' => $content,
                                                        ]); ?>
                                                    </td>
                                                </tr>
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