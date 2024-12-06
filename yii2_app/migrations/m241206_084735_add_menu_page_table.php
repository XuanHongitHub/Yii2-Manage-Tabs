<?php

use yii\db\Migration;

/**
 * Class m241206_084735_add_menu_page_table
 */
class m241206_084735_add_menu_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%manager_menu_page}}', [
            'id' => $this->primaryKey(),
            'menu_id' => $this->integer(),
            'page_id' => $this->integer(),
        ]);

        $this->dropIndex('idx_page_menu_id', '{{%manager_page}}');

        // Thêm khóa ngoại cho user_id (tham chiếu đến manager_user)
        $this->dropForeignKey(
            'fk_manager_page_menu_id',
            '{{%manager_page}}',
        );

        // Thêm khóa ngoại cho menu_id (tham chiếu đến manager_menu)
        $this->addForeignKey(
            'fk_manager_menu_page_menu_id',
            '{{%manager_menu_page}}',
            'menu_id',
            '{{%manager_menu}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Thêm khóa ngoại cho page_id (tham chiếu đến manager_page)
        $this->addForeignKey(
            'fk_manager_menu_page_page_id',
            '{{%manager_menu_page}}',
            'page_id',
            '{{%manager_page}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $pages = $this->db->createCommand('SELECT id, menu_id FROM {{%manager_page}}')->queryAll();
        foreach ($pages as $page) {
            $this->insert('{{%manager_menu_page}}', [
                'menu_id' => $page['menu_id'],
                'page_id' => $page['id'],
            ]);
        }

        $this->dropColumn('{{%manager_page}}', 'menu_id');
        $this->dropColumn('{{%manager_page}}', 'position');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241206_084735_add_menu_page_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241206_084735_add_menu_page_table cannot be reverted.\n";

        return false;
    }
    */
}
