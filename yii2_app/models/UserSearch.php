<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserSearch extends User
{
    public $searchQuery;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['searchQuery'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->searchQuery) {
            $query->andFilterWhere([
                'or',
                ['like', "LOWER(unaccent(username))", strtolower($this->searchQuery)],
                ['like', "LOWER(unaccent(email))", strtolower($this->searchQuery)],
            ]);
        }

        return $dataProvider;
    }
}