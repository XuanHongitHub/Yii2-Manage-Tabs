<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%manager_config}}`.
 */
class m241207_085948_create_manager_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo bảng manager_config
        $this->createTable('{{%manager_config}}', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer()->notNull(),
            'menu_id' => $this->integer()->notNull(),
            'column_name' => $this->string(255)->notNull(),
            'is_visible' => $this->boolean()->notNull()->defaultValue(true),
        ]);

        // Thêm chỉ mục và khóa ngoại cho bảng manager_config
        $this->createIndex('idx_manager_config_page_id', '{{%manager_config}}', 'page_id');
        $this->createIndex('idx_manager_config_menu_id', '{{%manager_config}}', 'menu_id');
        $this->addForeignKey(
            'fk_manager_config_page_id',
            '{{%manager_config}}',
            'page_id',
            '{{%manager_page}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_manager_config_menu_id',
            '{{%manager_config}}',
            'menu_id',
            '{{%manager_menu}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_manager_config_menu_id', '{{%manager_config}}');
        $this->dropForeignKey('fk_manager_config_page_id', '{{%manager_config}}');
        $this->dropTable('{{%manager_config}}');
    }
}