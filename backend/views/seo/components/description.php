<?php

use common\models\Seo;

/**
 * @var Seo $seoModel
 */

?>

<div class="row" style="margin: 0">
    <div class="col">
        <?php echo $form->field($seoModel, 'description')->textarea([
            'rows' => 3,
            'placeholder' => Yii::t('seo', 'DESCRIPTION_META_TAG_CONTENT')
        ])->label(Yii::t('element',
            'SEO Description')) ?>
    </div>
</div>
