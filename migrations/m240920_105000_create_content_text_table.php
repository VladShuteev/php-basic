<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_text}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%content}}`
 */
class m240920_105000_create_content_text_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_text}}', [
            'id' => $this->bigPrimaryKey(),
            'content_id' => $this->string()->notNull()->unique(),
            'content' => $this->text()->null(),
        ]);

        // add foreign key for table `content`
        $this->addForeignKey(
            '{{%fk-content_text-content_id}}',
            '{{%content_text}}',
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
            '{{%fk-content_text-content_id}}',
            '{{%content_text}}'
        );

        $this->dropTable('{{%content_text}}');
    }
}
