<?php

use yii\db\Migration;

/**
 * Class m241023_180815_add_meta_id_to_channel_table
 */
class m241023_180815_add_meta_id_to_channel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%channel}}', 'meta_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241023_180815_add_meta_id_to_channel_table cannot be reverted.\n";

        $this->dropColumn('{{%channel}}', 'meta_id');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241023_180815_add_meta_id_to_channel_table cannot be reverted.\n";

        return false;
    }
    */
}
