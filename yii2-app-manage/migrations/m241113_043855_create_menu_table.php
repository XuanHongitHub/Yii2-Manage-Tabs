<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%menu}}`.
 */
class m241113_043855_create_menu_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%menu}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'icon' => $this->string(255),
            'position' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
            'parent_id' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_menu_parent_id', '{{%menu}}', 'parent_id');
        $this->addForeignKey('fk_menu_parent_id', '{{%menu}}', 'parent_id', '{{%menu}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_menu_parent_id', '{{%menu}}');
        $this->dropTable('{{%menu}}');
    }
}
