<?php

use common\models\Seo;

/**
 * @var Seo $seoModel
 */

?>

<div class="row" style="margin: 0">
    <div class="col">
        <?php echo $form->field($seoModel, 'keywords')->textarea([
            'rows' => 3,
            'placeholder' => Yii::t('seo', 'KEYWORDS_META_TAG_CONTENT')
        ])->label(Yii::t('element',
            'SEO Keywords')) ?>
    </div>
</div>
