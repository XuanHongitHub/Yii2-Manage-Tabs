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
        $this->createTable('table_tab', [
            'id' => $this->primaryKey(),
            'tab_id' => $this->integer()->notNull(),
            'table_name' => $this->string()->notNull(),
            'column_name' => $this->string()->notNull(),
            'data_type' => $this->string(50)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=latin1');

        $this->addForeignKey(
            'fk-table_tab-tab_id',
            'table_tab',
            'tab_id',
            'tab',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-table_tab-tab_id', 'table_tab');
        $this->dropTable('table_tab');
    }
}