<?php

use app\models\BaseModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;

function tableDataShortcode($attributes)
{
    $table = $attributes['table'] ?? 'default_table';
    $excludedTables = ['manager_page', 'manager_user', 'manager_menu', 'manager_menu_page', 'manager_config', 'migration'];


    if (in_array($table, $excludedTables)) {
        return '<h2>Table not found!!</h2>';
    }
    $pageSize = $attributes['page_size'] ?? 10;
    if (isset($attributes['columns'])) {
        $columnNames = explode(',', $attributes['columns']);
    } else {
        $columns = Yii::$app->db->schema->getTableSchema($table)->columns;
        $columnNames = array_keys($columns);
    }
    $pjaxId = $attributes['pjax_id'] ?? 'pjax-table';


    $query = (new Query())->from($table);

    if (!empty($searchTerm)) {
        $condition = [];

        foreach ($columnNames as $columnName) {
            if ($columnName === BaseModel::HIDDEN_ID_KEY) {
                continue;
            }
            $columnNameQuoted = "\"$columnName\"";
            $condition[] = "LOWER(unaccent(CAST($columnNameQuoted AS TEXT))) ILIKE LOWER(unaccent(:searchTerm))";
        }

        if (!empty($condition)) {
            $query->where(implode(' OR ', $condition), [':searchTerm' => '%' . $searchTerm . '%']);
        }
    }

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => [
            'pageSize' => $pageSize,
        ],
        'sort' => [
            'attributes' => $columnNames,
        ],
    ]);

    return Yii::$app->shortcode->renderTemplatePart('tableData', ['dataProvider' => $dataProvider, 'pjaxId' => $pjaxId, 'columns' => $columnNames,]);
}