<?php

/** @var array $activeServices */
/** @var null|integer $currentCredits */

if (!empty($activeServices) && !is_null($currentCredits)): ?>
    <span id="PS-C-19" class="current-credits">
        <?php echo Yii::t('element', 'PS-C-19', [
            'currentCredits' => $currentCredits,
        ]); ?>
    </span>
<?php endif; ?>
<br/>
<?php if (!is_null($advCredits)): ?>
    <span id="PS-C-19" class="current-credits">
        <?php echo Yii::t('element', 'adv-credits-active-services', [
            'advCredits' => $advCredits,
        ]); ?>
    </span>
<?php endif; ?>
<div id="PS-C-20" class="your-subscription-list">
    <?php echo Yii::t('element', 'PS-C-20'); ?>
</div>

<div class="custom-table active-services-table table-responsive">
    <table id="active-services" class="table table-striped">
        <thead>
            <tr class="headline">
                <th id="PS-C-21">
                    <?php echo Yii::t('element', 'PS-C-21'); ?>
                </th>
                <th id="PS-C-23">
                    <?php echo Yii::t('element', 'PS-C-23'); ?>
                </th>
                <th id="PS-C-25">
                    <?php echo Yii::t('element', 'PS-C-25'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($activeServices)): ?>
                <tr class="active-service-row">
                    <td id="PS-C-25a" colspan="3">
                        <?php echo Yii::t('element', 'PS-C-25a'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($activeServices as $activeService): ?>
                    <tr class="active-service-row">
                        <td>
                            <?php if ($activeService->service->isTrial()): ?>
                                <?php echo 'TRIAL ' . $activeService->calculateTrialDays(); ?>
                            <?php else: ?>
                                <?php echo Yii::t('app', $activeService->service->getTitle()); ?>
                            <?php endif; ?>

                            <?php if ($activeService->service->isCreditsServiceType()): ?>
                                (<?php echo $activeService->service->credits . ' ' .  Yii::t('app', 'credits'); ?>)
                            <?php endif; ?>
                        </td>
                         <td>
                            <?php echo date('Y-m-d', $activeService->date_of_purchase); ?>
                        </td>
                        <td>
                            <?php echo date('Y-m-d', $activeService->end_date); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>    
