<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tab_visibility".
 *
 * @property int $id
 * @property int $user_id
 * @property int $tab_id
 * @property int|null $is_visible
 * @property int|null $sort_order
 *
 * @property Tab $tab
 * @property User $user
 */
class TabVisibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tab_visibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tab_id'], 'required'],
            [['user_id', 'tab_id', 'is_visible', 'sort_order'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['tab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tab::class, 'targetAttribute' => ['tab_id' => 'id']],
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
            'tab_id' => 'Tab ID',
            'is_visible' => 'Is Visible',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * Gets query for [[Tab]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTab()
    {
        return $this->hasOne(Tab::class, ['id' => 'tab_id']);
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
