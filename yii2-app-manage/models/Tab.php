<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tab".
 *
 * @property int $id
 * @property int $user_id
 * @property string $tab_type
 * @property int|null $deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property RichtextTab[] $richtextTabs
 * @property TabVisibility[] $tabVisibilities
 * @property TableTab[] $tableTabs
 * @property User $user
 */
class Tab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tab_type'], 'required'],
            [['user_id', 'deleted'], 'integer'],
            [['tab_type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'tab_type' => 'Tab Type',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[RichtextTabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRichtextTabs()
    {
        return $this->hasMany(RichtextTab::class, ['tab_id' => 'id']);
    }

    /**
     * Gets query for [[TabVisibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTabVisibilities()
    {
        return $this->hasMany(TabVisibility::class, ['tab_id' => 'id']);
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
