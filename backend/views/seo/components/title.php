<?php

use common\models\Seo;

/**
 * @var Seo $seoModel
 */

?>

<div class="row" style="margin: 0">
    <div class="col">
        <?php echo $form->field($seoModel, 'title')->label(Yii::t('element', 'SEO Title')) ?>
    </div>
</div>
