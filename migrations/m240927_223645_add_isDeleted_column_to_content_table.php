<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%content}}`.
 */
class m240927_223645_add_isDeleted_column_to_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%content}}", "isDeleted", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%content}}", "isDeleted");
    }
}
