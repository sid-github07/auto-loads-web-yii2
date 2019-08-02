<?php

namespace common\models;

use common\components\Languages;

/**
 * This is the model class for table "seo".
 *
 * @property string $page
 * @property string $route
 * @property string $domain
 * @property string $title
 * @property string $keywords
 * @property string $description
 */
class Seo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route', 'domain', 'title'], 'required'],
            [['keywords', 'description'], 'string'],
            [['page', 'route'], 'string', 'max' => 50],
            [['domain'], 'string', 'max' => 10],
            ['domain', 'in', 'range' => array_keys(Languages::getLanguages())],
            [['title'], 'string', 'max' => 100],
            [['page', 'domain'], 'unique', 'targetAttribute' => ['page', 'domain']],
            [['route', 'domain'], 'unique', 'targetAttribute' => ['route', 'domain']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'page' => 'Page',
            'route' => 'Route',
            'domain' => 'Domain',
            'title' => 'Title',
            'keywords' => 'Keywords',
            'description' => 'Description',
        ];
    }

    /**
     * @return array
     */
    public static function listPages()
    {
        return self::find()->select('page')->indexBy('page')->column();
    }
}
