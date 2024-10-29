<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m241029_093845_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email' => $this->string()->defaultValue(null)->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(512)->defaultValue(null),
            'verification_token' => $this->string()->defaultValue(null),
            'password_hash' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->integer()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'password_reset_token' => $this->string()->defaultValue(null)->unique(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=latin1');
    }

    public function safeDown()
    {
        $this->dropTable('user');
    }
}