<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trigger}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%automation}}`
 * - `{{%account}}`
 */
class m240920_106000_create_trigger_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trigger}}', [
            'id' => $this->bigPrimaryKey(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'type' => $this->text()->null(),
            'value' => $this->text()->null(),
            'automation_id' => $this->bigInteger()->null(),
            'account_id' => $this->bigInteger()->null(),
        ]);

        // add foreign key for table `automation`
        $this->addForeignKey(
            '{{%fk-trigger-automation_id}}',
            '{{%trigger}}',
            'automation_id',
            '{{%automation}}',
            'id',
            'SET NULL'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            '{{%fk-trigger-account_id}}',
            '{{%trigger}}',
            'account_id',
            '{{%account}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `automation`
        $this->dropForeignKey(
            '{{%fk-trigger-automation_id}}',
            '{{%trigger}}'
        );

        // drops foreign key for table `account`
        $this->dropForeignKey(
            '{{%fk-trigger-account_id}}',
            '{{%trigger}}'
        );

        $this->dropTable('{{%trigger}}');
    }
}
