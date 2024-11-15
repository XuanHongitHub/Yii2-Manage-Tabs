<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tab_menus}}`.
 */
class m241113_043855_create_tab_menus_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo bảng với bộ mã hóa UTF-8
        $this->createTable('tab_menus', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'icon' => $this->string()->defaultValue(null),
            'position' => $this->integer()->defaultValue(0),
            'deleted' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $this->createIndex(
            'idx-tab_menus-name',
            'tab_menus',
            'name',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Xóa chỉ mục unique
        $this->dropIndex('idx-tab_menus-name', 'tab_menus');

        // Xóa bảng
        $this->dropTable('tab_menus');
    }
}
