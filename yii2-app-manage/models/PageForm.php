<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PageForm extends Model
{
    public $pageName;
    public $type;
    public $tableName;
    public $columns;

    public function rules()
    {
        return [
            [['pageName', 'type'], 'required'],
            ['pageName', 'match', 'pattern' => '/^[a-zA-ZÀ-ỹà-ỹ0-9_ ]{1,20}$/', 'message' => 'Tên Page không hợp lệ.'],
            ['tableName', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Tên Bảng không hợp lệ.'],
            ['columns', 'validateColumns'],
        ];
    }

    public function validateColumns($attribute, $params)
    {
        if ($this->type === 'table' && is_array($this->columns)) {
            foreach ($this->columns as $column) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $column['name'])) {
                    $this->addError($attribute, 'Tên cột không hợp lệ.');
                }
                // Kiểm tra kiểu dữ liệu và kích thước cột
                if (isset($column['data_size']) && (!is_numeric($column['data_size']) || $column['data_size'] < 1 || $column['data_size'] > 1000)) {
                    $this->addError($attribute, 'Kích thước cột không hợp lệ.');
                }
            }
        }
    }
}
