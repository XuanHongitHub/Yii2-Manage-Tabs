<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%table_tab}}`.
 */
class m241029_094026_create_table_tab_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%table_tab}}', [
            'id' => $this->primaryKey(),
            'tab_id' => $this->integer()->notNull(),
            'table_name' => $this->string(255)->notNull(),
            'column_name' => $this->string(255)->notNull(),
            'data_type' => $this->string(50)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_table_tab_tab_id', '{{%table_tab}}', 'tab_id');
        $this->addForeignKey('fk_table_tab_tab_id', '{{%table_tab}}', 'tab_id', '{{%page}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_table_tab_tab_id', '{{%table_tab}}');
        $this->dropTable('{{%table_tab}}');
    }
}
