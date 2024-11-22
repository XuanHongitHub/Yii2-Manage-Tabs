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
        $this->createTable('{{%user}}', [
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
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
