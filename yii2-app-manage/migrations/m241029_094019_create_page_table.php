<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page}}` and `menu`.
 */
class m241029_094019_create_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo kiểu dữ liệu ENUM trong PostgreSQL (nếu cần)
        $this->execute("CREATE TYPE page_type AS ENUM ('table', 'richtext')");

        // Tạo bảng
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->unique(),
            'type' => "page_type NOT NULL",
            'menu_id' => $this->integer(),
            'icon' => $this->string(255),
            'status' => $this->smallInteger()->defaultValue(0),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'position' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Thêm index và khóa ngoại
        $this->createIndex('idx_page_user_id', '{{%page}}', 'user_id');
        $this->createIndex('idx_page_menu_id', '{{%page}}', 'menu_id');

        $this->addForeignKey('fk_page_user_id', '{{%page}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_page_menu_id', '{{%page}}', 'menu_id', '{{%menu}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        // Xóa khóa ngoại
        $this->dropForeignKey('fk_page_user_id', '{{%page}}');
        $this->dropForeignKey('fk_page_menu_id', '{{%page}}');

        // Xóa bảng
        $this->dropTable('{{%page}}');

        // Xóa kiểu dữ liệu ENUM
        $this->execute("DROP TYPE page_type");
    }
}
