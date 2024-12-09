<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manager_config".
 *
 * @property int $id
 * @property int $page_id
 * @property int $menu_id
 * @property string $column_name
 * @property bool $is_visible
 *
 * @property Menu $menu
 * @property Page $page
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_id', 'column_name'], 'required'],
            [['page_id', 'menu_id'], 'default', 'value' => null],
            [['page_id', 'menu_id'], 'integer'],
            [['is_visible'], 'boolean'],
            [['column_name'], 'string', 'max' => 255],
            [['column_name'], 'unique', 'targetAttribute' => ['menu_id', 'page_id', 'column_name'], 'message' => 'Cột này đã tồn tại trong cấu hình.'],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::class, 'targetAttribute' => ['menu_id' => 'id']],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'menu_id' => 'Menu ID',
            'column_name' => 'Column Name',
            'is_visible' => 'Is Visible',
        ];
    }

    /**
     * Gets query for [[Menu]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::class, ['id' => 'menu_id']);
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }
}
