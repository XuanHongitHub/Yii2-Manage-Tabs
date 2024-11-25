<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property int|null $position
 * @property int|null $status
 * @property int|null $deleted
 * @property int|null $parent_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Page[] $pages
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['position', 'status', 'deleted', 'parent_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'icon'], 'string', 'max' => 255],
            [['name'], 'unique'],
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
            'status' => 'Status',
            'deleted' => 'Deleted',
            'parent_id' => 'Parent ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Pages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTabs()
    {
        return $this->hasMany(Page::class, ['menu_id' => 'id']);
    }
    public function getChildMenus()
    {
        return $this->hasMany(Menu::class, ['parent_id' => 'id']);
    }
}
