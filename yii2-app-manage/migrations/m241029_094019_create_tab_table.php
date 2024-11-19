<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tab}}` and `menu`.
 */
class m241029_094019_create_tab_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tab', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'tab_name' => $this->string()->defaultValue(null)->unique(),
            'tab_type' => "ENUM('table', 'richtext') NOT NULL",
            'deleted' => $this->tinyInteger()->defaultValue(0),
            'position' => $this->integer()->defaultValue(0),
            'menu_id' => $this->integer()->defaultValue(null),
            'icon' => $this->string()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->addForeignKey(
            'fk-tab-menu_id',
            'tab',
            'menu_id',
            'menu',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-tab-user_id',
            'tab',
            'user_id',
            'user',
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
        $this->dropForeignKey('fk-tab-user_id', 'tab');

        $this->dropForeignKey('fk-tab-menu_id', 'tab');

        $this->dropTable('tab');

        $this->dropTable('menu');
    }
}
