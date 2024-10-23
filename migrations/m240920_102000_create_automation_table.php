<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%automation}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%account}}`
 * - `{{%content}}` (добавим FK после создания `content`)
 */
class m240920_102000_create_automation_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%automation}}', [
            'id' => $this->bigPrimaryKey(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'name' => $this->text()->null(),
            'account_id' => $this->bigInteger()->null(),
            'content_id' => $this->string()->null(),
        ]);

        // add foreign key for table `account`
        $this->addForeignKey(
            '{{%fk-automation-account_id}}',
            '{{%automation}}',
            'account_id',
            '{{%account}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `account`
        $this->dropForeignKey(
            '{{%fk-automation-account_id}}',
            '{{%automation}}'
        );

        $this->dropTable('{{%automation}}');
    }
}
