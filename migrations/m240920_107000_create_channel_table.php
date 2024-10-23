<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%channel}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%account}}`
 */
class m240920_107000_create_channel_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%channel}}', [
            'id' => $this->bigPrimaryKey(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'account_id' => $this->bigInteger()->notNull(),
            'token' => $this->text()->notNull(),
        ]);

        // add foreign key for table `account`
        $this->addForeignKey(
            '{{%fk-channel-account_id}}',
            '{{%channel}}',
            'account_id',
            '{{%account}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `account`
        $this->dropForeignKey(
            '{{%fk-channel-account_id}}',
            '{{%channel}}'
        );

        $this->dropTable('{{%channel}}');
    }
}
