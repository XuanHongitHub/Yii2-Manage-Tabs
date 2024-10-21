<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "richtext_tab".
 *
 * @property int $id
 * @property int $tab_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tab $tab
 */
class RichtextTab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'richtext_tab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tab_id', 'content'], 'required'],
            [['tab_id'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'content' => 'Content',
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
