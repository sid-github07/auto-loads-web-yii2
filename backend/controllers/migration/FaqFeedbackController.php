<?php

namespace backend\controllers\migration;

use common\models\FaqFeedback;
use Yii;

/**
 * Class FaqFeedbackController
 *
 * This controller is responsible for migrating FAQ questions
 *
 * @package backend\controllers\migration
 */
class FaqFeedbackController extends MigrateController
{
    /**
     * Migrates FAQ questions from old system to new one
     *
     * @return null
     */
    public function actionFaqFeedback()
    {
        $faqFeedBacks = Yii::$app->db_prod->createCommand("SELECT * FROM faq_feed_backs")->queryAll();
        foreach ($faqFeedBacks as $faqFeedBack) {
            if ($this->faqFeedbackExists($faqFeedBack['faq_feed_backs_id'])) {
                continue;
            }

            $this->migrateFaqFeedback($faqFeedBack);
        }

        return null;
    }

    /**
     * Checks whether FAQ feedback has been already migrated
     *
     * @param null|integer $id FAQ feedback ID
     * @return boolean
     */
    private function faqFeedbackExists($id)
    {
        return FaqFeedback::find()->where(compact('id'))->exists();
    }

    /**
     * Migrates FAQ question from old system to new one
     *
     * @param array $feedback Information about FAQ question
     * @return boolean|null
     */
    private function migrateFaqFeedback($feedback)
    {
        $faqFeedback = new FaqFeedback(['scenario' => FaqFeedback::SCENARIO_SYSTEM_MIGRATES_FAQ_FEEDBACK]);

        $faqFeedback->id = $feedback['faq_feed_backs_id'];
        $faqFeedback->question = $this->convertQuestion($feedback['faq_id']);
        if (empty($feedback['email'])) {
            $this->writeToCSV(FaqFeedback::tableName(), 'Klausimui, kurio ID: ' . $faqFeedback->id . ' nenurodytas el. pašto adresas');
            return null;
        } else {
            $faqFeedback->email = trim($feedback['email']);
        }

        if (!$faqFeedback->validate(['email'])) {
            $this->writeToCSV(FaqFeedback::tableName(), 'Klausimui, kurio ID: ' . $faqFeedback->id . ' nurodytas neteisingas el. pašto adresas');
            return null;
        }

        if (empty($feedback['comment'])) {
            $this->writeToCSV(FaqFeedback::tableName(), 'Klausimas, kurio ID: ' . $faqFeedback->id . ' neturi komentaro');
            return null;
        } else {
            $faqFeedback->comment = $feedback['comment'];
        }

        $faqFeedback->solved = $this->convertSolved($feedback['solved']);
        $faqFeedback->created_at = strtotime($feedback['date']);
        $faqFeedback->updated_at = strtotime($feedback['date']);

        $faqFeedback->validate();
        if ($faqFeedback->errors) {
            var_dump($faqFeedback->id);
            var_dump($faqFeedback->errors);
        } else {
            $faqFeedback->save();
        }
    }

    /**
     * Converts FAQ question ID from old system to new one
     *
     * @param integer $faqId Old system FAQ question ID
     * @return integer
     */
    private function convertQuestion($faqId)
    {
        $placeholder = FaqFeedback::getFaqQuestionsPlaceholders();

        for ($i = 1; $i <= 22; $i++) { // There are 22 different questions
            $oldId = $i;
            for ($j = 1; $j <= 6; $j++) { // There are 6 different languages
                if ($oldId == $faqId) {
                    return $placeholder[$i - 1]; // In this system questions start from 0
                }
                $oldId += 22; // Go to the same question but in different language
            }
        }

        return 1;
    }

    /**
     * Converts FAQ question solutions status from old system to new one
     *
     * In old system there was different FAQ question solutions status system:
     * 1 - unresolved
     * 2 - solved
     *
     * @param integer $solved Old FAQ question solution status
     * @return boolean
     */
    private function convertSolved($solved)
    {
        return ($solved == 1) ? FaqFeedback::NOT_RESOLVED : FaqFeedback::SOLVED;
    }
}