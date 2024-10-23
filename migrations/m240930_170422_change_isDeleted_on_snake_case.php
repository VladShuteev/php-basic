<?php

use yii\db\Migration;

/**
 * Class m240930_170422_change_isDeleted_on_snake_case
 */
class m240930_170422_change_isDeleted_on_snake_case extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('content', 'isDeleted', 'is_deleted');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('content', 'is_deleted', 'isDeleted');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240930_170422_change_isDeleted_on_snake_case cannot be reverted.\n";

        return false;
    }
    */
}
