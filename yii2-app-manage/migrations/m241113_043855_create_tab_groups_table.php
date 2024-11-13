<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tab_groups}}`.
 */
class m241113_043855_create_tab_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo bảng với bộ mã hóa UTF-8
        $this->createTable('tab_groups', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'icon' => $this->string()->defaultValue(null),
            'position' => $this->integer()->defaultValue(0),
            'deleted' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $this->createIndex(
            'idx-tab_groups-name',
            'tab_groups',
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
        $this->dropIndex('idx-tab_groups-name', 'tab_groups');

        // Xóa bảng
        $this->dropTable('tab_groups');
    }
}