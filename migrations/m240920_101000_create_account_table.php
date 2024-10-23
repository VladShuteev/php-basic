<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%account}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240920_101000_create_account_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%account}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'name' => $this->text()->notNull(),
            'avatar' => $this->text()->notNull(),
        ]);

        // add foreign key for table `user`
        $this->addForeignKey(
            '{{%fk-account-user_id}}',
            '{{%account}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-account-user_id}}',
            '{{%account}}'
        );

        $this->dropTable('{{%account}}');
    }
}
