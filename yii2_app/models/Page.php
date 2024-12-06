<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $table_name
 * @property string|null $content
 * @property string $type
 * @property int|null $status
 * @property int|null $deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property MenuPage[] $managerMenuPages
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'name', 'type'], 'required'],
            [['user_id', 'deleted', 'status'], 'integer'],
            [['type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'table_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Page Name',
            'table_name' => 'Table Name',
            'content' => 'Content',
            'type' => 'Page Type',
            'deleted' => 'Deleted',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[MenuPages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuPages()
    {
        return $this->hasMany(MenuPage::class, ['page_id' => 'id']);
    }
}