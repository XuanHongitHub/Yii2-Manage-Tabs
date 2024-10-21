<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "table_tab".
 *
 * @property int $id
 * @property int $tab_id
 * @property string $table_name
 * @property string $column_name
 * @property string $data_type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tab $tab
 */
class TableTab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'table_tab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tab_id', 'table_name', 'column_name', 'data_type'], 'required'],
            [['tab_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['table_name', 'column_name'], 'string', 'max' => 255],
            [['data_type'], 'string', 'max' => 50],
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
            'tab_id' => 'Tab ID',
            'table_name' => 'Table Name',
            'column_name' => 'Column Name',
            'data_type' => 'Data Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
}
