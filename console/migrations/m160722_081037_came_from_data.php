<?php

use yii\db\Migration;

/**
 * Class m160722_081037_came_from_data
 */
class m160722_081037_came_from_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        // English
        $this->insert('{{%came_from}}', ['id' => 1, 'language_id' => 1, 'source_name' => 'A friend recommended', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 2, 'language_id' => 1, 'source_name' => 'Via search engine (i.e., Google)', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 3, 'language_id' => 1, 'source_name' => 'Saw an advertisement online', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 4, 'language_id' => 1, 'source_name' => 'Saw a local advertisement', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 5, 'language_id' => 1, 'source_name' => 'Via Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 6, 'language_id' => 1, 'source_name' => 'Via YouTube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 7, 'language_id' => 1, 'source_name' => 'Other', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);

        // Pусский
        $this->insert('{{%came_from}}', ['id' => 8, 'language_id' => 2, 'source_name' => 'Друг рекомендовал', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 9, 'language_id' => 2, 'source_name' => 'Через google', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 10, 'language_id' => 2, 'source_name' => 'Увидел рекламу в интернете', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 11, 'language_id' => 2, 'source_name' => 'Увидел рекламу', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 12, 'language_id' => 2, 'source_name' => 'Через Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 13, 'language_id' => 2, 'source_name' => 'Через YouTube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 14, 'language_id' => 2, 'source_name' => 'Другой', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);

        // Lietuvių
        $this->insert('{{%came_from}}', ['id' => 15, 'language_id' => 3, 'source_name' => 'Rekomendavo draugas', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 16, 'language_id' => 3, 'source_name' => 'Radau per paieškos sistemą (pvz., Google)', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 17, 'language_id' => 3, 'source_name' => 'Pamačiau reklamą Internete', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 18, 'language_id' => 3, 'source_name' => 'Pamačiau iškabintą reklamą', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 19, 'language_id' => 3, 'source_name' => 'Per socialinį tinklą Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 20, 'language_id' => 3, 'source_name' => 'Per YouTube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 21, 'language_id' => 3, 'source_name' => 'Kitas', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);

        // Polski
        $this->insert('{{%came_from}}', ['id' => 22, 'language_id' => 4, 'source_name' => 'Polecił znajomy', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 23, 'language_id' => 4, 'source_name' => 'Znalazłem przez system wyszukiwania (np. Google)', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 24, 'language_id' => 4, 'source_name' => 'Zobaczyłem reklamę w internecie', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 25, 'language_id' => 4, 'source_name' => 'Zobaczyłem wywieszone ogłoszenie', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 26, 'language_id' => 4, 'source_name' => 'Przez sieć społeczną Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 27, 'language_id' => 4, 'source_name' => 'Przez Youtube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 28, 'language_id' => 4, 'source_name' => 'Inny', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);

        // Deutsch
        $this->insert('{{%came_from}}', ['id' => 29, 'language_id' => 5, 'source_name' => 'Durch Empfehlung eines Freundes', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 30, 'language_id' => 5, 'source_name' => 'Habe durch Suchmaschine (z.B. Google) gefunden.', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 31, 'language_id' => 5, 'source_name' => 'Habe die Werbung im Internet gesehen', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 32, 'language_id' => 5, 'source_name' => 'Habe hängende Werbung gesehen', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 33, 'language_id' => 5, 'source_name' => 'Im Sozialnetz Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 34, 'language_id' => 5, 'source_name' => 'Im You Tube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 35, 'language_id' => 5, 'source_name' => 'weiter', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);

        // Español
        $this->insert('{{%came_from}}', ['id' => 36, 'language_id' => 7, 'source_name' => 'Me lo recomendó un amigo', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 37, 'language_id' => 7, 'source_name' => 'Encontré a través de un buscador (por ejemplo, Google)', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 38, 'language_id' => 7, 'source_name' => 'Vi anuncios en Internet', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 39, 'language_id' => 7, 'source_name' => 'Vi publicidad en la calle', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 40, 'language_id' => 7, 'source_name' => 'A través de la red social Facebook', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 41, 'language_id' => 7, 'source_name' => 'A través de YouTube', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 42, 'language_id' => 7, 'source_name' => 'Otro', 'type' => 0, 'created_at' => time(), 'updated_at' => time()]);
        
        // English
        $this->insert('{{%came_from}}', ['id' => 85, 'language_id' => 1, 'source_name' => 'I am Looking for a work for car transporters', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 86, 'language_id' => 1, 'source_name' => 'Constantly in need to transport cars', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 87, 'language_id' => 1, 'source_name' => 'Other', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);

        // Pусский
        $this->insert('{{%came_from}}', ['id' => 88, 'language_id' => 2, 'source_name' => 'I am Looking for a work for car transporters', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 89, 'language_id' => 2, 'source_name' => 'Constantly in need to transport cars', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 90, 'language_id' => 2, 'source_name' => 'Other', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);

        // Lietuvių
        $this->insert('{{%came_from}}', ['id' => 91, 'language_id' => 3, 'source_name' => 'Ieškau darbo autovežiams', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 92, 'language_id' => 3, 'source_name' => 'Nuolat reikia pervežti automobilius', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 93, 'language_id' => 3, 'source_name' => 'Kitas', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);

        // Polski
        $this->insert('{{%came_from}}', ['id' => 94, 'language_id' => 4, 'source_name' => 'I am Looking for a work for car transporters', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 95, 'language_id' => 4, 'source_name' => 'Constantly in need to transport cars', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 96, 'language_id' => 4, 'source_name' => 'Other', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);

        // Deutsch
        $this->insert('{{%came_from}}', ['id' => 97, 'language_id' => 5, 'source_name' => 'I am Looking for a work for car transporters', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 98, 'language_id' => 5, 'source_name' => 'Constantly in need to transport cars', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 99, 'language_id' => 5, 'source_name' => 'Other', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);

        // Español
        $this->insert('{{%came_from}}', ['id' => 103, 'language_id' => 7, 'source_name' => 'I am Looking for a work for car transporters', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 104, 'language_id' => 7, 'source_name' => 'Constantly in need to transport cars', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%came_from}}', ['id' => 105, 'language_id' => 7, 'source_name' => 'Other', 'type' => 1, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable('{{%came_from}}');
    }
}
