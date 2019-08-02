<?php

use common\models\Language;
use yii\db\Migration;

/**
 * Class m160720_071603_language_data
 */
class m160720_071603_language_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->insert(Language::tableName(), ['id' => 1, 'country_code' => 'US', 'name' => 'English']);
        $this->insert(Language::tableName(), ['id' => 2, 'country_code' => 'RU', 'name' => 'Pусский']);
        $this->insert(Language::tableName(), ['id' => 3, 'country_code' => 'LT', 'name' => 'Lietuvių']);
        $this->insert(Language::tableName(), ['id' => 4, 'country_code' => 'PL', 'name' => 'Polski']);
        $this->insert(Language::tableName(), ['id' => 5, 'country_code' => 'DE', 'name' => 'Deutsch']);
        $this->insert(Language::tableName(), ['id' => 6, 'country_code' => 'LV', 'name' => 'Latviešu']);
        $this->insert(Language::tableName(), ['id' => 7, 'country_code' => 'ES', 'name' => 'Español']);
        $this->insert(Language::tableName(), ['id' => 8, 'country_code' => 'IT', 'name' => 'Italiano']);
        $this->insert(Language::tableName(), ['id' => 9, 'country_code' => 'CZ', 'name' => 'Čeština']);
        $this->insert(Language::tableName(), ['id' => 10, 'country_code' => 'FR', 'name' => 'Français']);
        $this->insert(Language::tableName(), ['id' => 11, 'country_code' => 'NL', 'name' => 'Nederlands']);
        $this->insert(Language::tableName(), ['id' => 12, 'country_code' => 'EE', 'name' => 'Eesti']);
        $this->insert(Language::tableName(), ['id' => 13, 'country_code' => 'BG', 'name' => 'Balgarski']);
        $this->insert(Language::tableName(), ['id' => 14, 'country_code' => 'SI', 'name' => 'Svenska']);
        $this->insert(Language::tableName(), ['id' => 15, 'country_code' => 'SK', 'name' => 'Slovenčina']);
        $this->insert(Language::tableName(), ['id' => 16, 'country_code' => 'RS', 'name' => 'Srpski']);
        $this->insert(Language::tableName(), ['id' => 17, 'country_code' => 'FI', 'name' => 'Suomi']);
        $this->insert(Language::tableName(), ['id' => 18, 'country_code' => 'PT', 'name' => 'Português']);
        $this->insert(Language::tableName(), ['id' => 19, 'country_code' => 'DK', 'name' => 'Dansk']);
        $this->insert(Language::tableName(), ['id' => 20, 'country_code' => 'NO', 'name' => 'Bokmål']);
        $this->insert(Language::tableName(), ['id' => 21, 'country_code' => 'TR', 'name' => 'Türkçe']);
        $this->insert(Language::tableName(), ['id' => 22, 'country_code' => 'SI', 'name' => 'Slovenščina']);
        $this->insert(Language::tableName(), ['id' => 23, 'country_code' => 'RO', 'name' => 'Româna']);
        $this->insert(Language::tableName(), ['id' => 24, 'country_code' => 'IS', 'name' => 'Íslenska']);
        $this->insert(Language::tableName(), ['id' => 25, 'country_code' => 'HR', 'name' => 'Hrvatski']);
        $this->insert(Language::tableName(), ['id' => 26, 'country_code' => 'HR', 'name' => 'Bosanski']);
        $this->insert(Language::tableName(), ['id' => 27, 'country_code' => 'GR', 'name' => 'Ellinika']);
        $this->insert(Language::tableName(), ['id' => 28, 'country_code' => 'HU', 'name' => 'Magyar']);
        $this->insert(Language::tableName(), ['id' => 29, 'country_code' => 'GE', 'name' => 'Kartuli']);
        $this->insert(Language::tableName(), ['id' => 30, 'country_code' => 'AM', 'name' => 'Haieren']);
        $this->insert(Language::tableName(), ['id' => 31, 'country_code' => 'MK', 'name' => 'Makedonski']);
        $this->insert(Language::tableName(), ['id' => 32, 'country_code' => 'AL', 'name' => 'Shqip']);
        $this->insert(Language::tableName(), ['id' => 33, 'country_code' => 'MV', 'name' => 'Malti']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable(Language::tableName());
    }
}
