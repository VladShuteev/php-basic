<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_delay}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%content}}`
 */
class m240920_104000_create_content_delay_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_delay}}', [
            'id' => $this->bigPrimaryKey(),
            'content_id' => $this->string()->notNull()->unique(),
            'duration' => $this->bigInteger()->null(),
        ]);

        // add foreign key for table `content`
        $this->addForeignKey(
            '{{%fk-content_delay-content_id}}',
            '{{%content_delay}}',
            'content_id',
            '{{%content}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `content`
        $this->dropForeignKey(
            '{{%fk-content_delay-content_id}}',
            '{{%content_delay}}'
        );

        $this->dropTable('{{%content_delay}}');
    }
}
