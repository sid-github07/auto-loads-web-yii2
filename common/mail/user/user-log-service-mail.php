<?php

use yii\helpers\Url;

/* @var $url string */
/* @var $companyName string */
?>

<div width="100%" style="
    background: #f0f0f0;
    font-family: 'Open Sans';
    height: 100%;
    margin: 0 auto;
    padding: 0;
    width: 100%;
">
    <table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" style="
       border-collapse: collapse;
       border-spacing: 0;
       margin: 0 auto;
       background: #f0f0f0;"
    >
        <tbody>
            <tr>
                <td>
                    <div style="margin: auto; max-width: 600px;">
                        <table width="100%"
                               style="width: 100%;
                               border-collapse: collapse;
                               border-spacing: 0;"
                        >
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
                                <tr>
                                  <td>
                                    <?php echo Yii::t('element', 'email_of_notify_by_email'); ?>
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