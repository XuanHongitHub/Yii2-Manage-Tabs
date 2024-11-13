<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tab_groups".
 *
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property int|null $position
 * @property int|null $deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tab[] $tabs
 */
class TabGroups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tab_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['position', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'icon' => 'Icon',
            'position' => 'Position',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Tabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTabs()
    {
        return $this->hasMany(Tab::class, ['group_id' => 'id']);
    }
}
