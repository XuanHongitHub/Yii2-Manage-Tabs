<?php

use yii\db\Migration;

/**
 * Class m241125_140853_create_manager_page_manager_menu_manager_user_tables
 */
class m241125_140853_create_manager_page_manager_menu_manager_user_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo kiểu dữ liệu ENUM
        $this->execute("CREATE TYPE page_type AS ENUM ('table', 'richtext')");

        // Tạo bảng manager_user trước
        $this->createTable('{{%manager_user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'email' => $this->string(255)->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(512),
            'verification_token' => $this->string(255),
            'password_hash' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->integer()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
        ]);

        // Tạo bảng manager_menu
        $this->createTable('{{%manager_menu}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'icon' => $this->string(255),
            'position' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(0),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'parent_id' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Thêm chỉ mục cho parent_id
        $this->createIndex('idx-manager_menu-parent_id', '{{%manager_menu}}', 'parent_id');

        // Thêm khóa ngoại cho parent_id (tham chiếu đến manager_menu)
        $this->addForeignKey(
            'fk_manager_menu_parent_id',
            '{{%manager_menu}}',
            'parent_id',
            '{{%manager_menu}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Tạo bảng manager_page
        $this->createTable('{{%manager_page}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->unique(),
            'table_name' => $this->string(255),
            'content' => $this->text(),
            'type' => "page_type NOT NULL",
            'menu_id' => $this->integer(),
            'status' => $this->smallInteger()->defaultValue(0),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'position' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Thêm các chỉ mục
        $this->createIndex('idx_page_user_id', '{{%manager_page}}', 'user_id');
        $this->createIndex('idx_page_menu_id', '{{%manager_page}}', 'menu_id');

        // Thêm khóa ngoại cho user_id (tham chiếu đến manager_user)
        $this->addForeignKey(
            'fk_manager_page_user_id',
            '{{%manager_page}}',
            'user_id',
            '{{%manager_user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Thêm khóa ngoại cho menu_id (tham chiếu đến manager_menu)
        $this->addForeignKey(
            'fk_manager_page_menu_id',
            '{{%manager_page}}',
            'menu_id',
            '{{%manager_menu}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Xóa các bảng có khóa ngoại tham chiếu
        $this->dropForeignKey('fk_manager_page_menu_id', '{{%manager_page}}');
        $this->dropForeignKey('fk_manager_page_user_id', '{{%manager_page}}');
        $this->dropForeignKey('fk_manager_menu_parent_id', '{{%manager_menu}}');

        // Xóa bảng manager_page và manager_menu trước
        $this->dropTable('{{%manager_page}}');
        $this->dropTable('{{%manager_menu}}');

        // Cuối cùng xóa bảng manager_user
        $this->dropTable('{{%manager_user}}');

        // Xóa kiểu dữ liệu ENUM
        $this->execute("DROP TYPE page_type");
    }
}