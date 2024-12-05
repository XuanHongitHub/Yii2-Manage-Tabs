<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class PageSearch extends Page
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Page::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10, // Số lượng bản ghi mỗi trang
            ],
            'sort' => [
                'attributes' => ['id', 'name'],
            ],
        ]);

        // Nạp dữ liệu từ các tham số và áp dụng bộ lọc
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Bộ lọc
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}