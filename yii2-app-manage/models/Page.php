<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $tab_type
 * @property int|null $status
 * @property int|null $deleted
 * @property int|null $menu_id
 * @property int|null $position
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TableTab[] $tableTabs
 * @property User $user
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tab_type'], 'required'],
            [['user_id', 'menu_id', 'deleted', 'status', 'position'], 'integer'],
            [['tab_type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'tab_type' => 'Page Type',
            'menu_id' => 'Group ID',
            'deleted' => 'Deleted',
            'status' => 'Status',
            'position' => 'Position',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[TableTabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTableTabs()
    {
        return $this->hasMany(TableTab::class, ['tab_id' => 'id']);
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
}
