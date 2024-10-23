<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%automation}}`
 * - self-referencing foreign key `next_content_id`
 */
class m240920_103000_create_content_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content}}', [
            'id' => $this->string()->notNull(),
            'automation_id' => $this->bigInteger()->null(),
            'type' => $this->string()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            // 'is_deleted' field is excluded
            'next_content_id' => $this->string()->null(),
            'PRIMARY KEY(id)',
        ]);

        // add foreign key for table `automation`
        $this->addForeignKey(
            '{{%fk-content-automation_id}}',
            '{{%content}}',
            'automation_id',
            '{{%automation}}',
            'id',
            'SET NULL'
        );

        // add self-referencing foreign key
        $this->addForeignKey(
            '{{%fk-content-next_content_id}}',
            '{{%content}}',
            'next_content_id',
            '{{%content}}',
            'id',
            'SET NULL'
        );

        // Теперь обновляем таблицу automation, добавляя внешний ключ на content
        $this->addForeignKey(
            '{{%fk-automation-content_id}}',
            '{{%automation}}',
            'content_id',
            '{{%content}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `content` in `automation`
        $this->dropForeignKey(
            '{{%fk-automation-content_id}}',
            '{{%automation}}'
        );

        // drops self-referencing foreign key
        $this->dropForeignKey(
            '{{%fk-content-next_content_id}}',
            '{{%content}}'
        );

        // drops foreign key for table `automation`
        $this->dropForeignKey(
            '{{%fk-content-automation_id}}',
            '{{%content}}'
        );

        $this->dropTable('{{%content}}');
    }
}
