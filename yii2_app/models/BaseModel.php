<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    const HIDDEN_ID_KEY = 'id';
    private static $customTableName;

    public static function withTable($tableName)
    {
        $instance = new static();
        self::$customTableName = $tableName;
        return $instance;
    }

    public static function tableName()
    {
        return self::$customTableName;
    }

    /**
     * Phương thức __get để xử lý cột động không hợp lệ.
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->getTableSchema()->columns)) {
            return $this->getAttribute($name);
        }

        return parent::__get($name);
    }

    /**
     * Phương thức __set để xử lý việc gán giá trị cho cột động.
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->getTableSchema()->columns)) {
            return $this->setAttribute($name, $value);
        }

        return parent::__set($name, $value);
    }

    /**
     * Tự động tạo rules dựa vào cấu trúc bảng
     *
     * @return array
     */
    public function rules()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema(self::$customTableName);
        $rules = [];

        if (!$tableSchema) {
            return $rules;
        }

        // Rule cho các cột bắt buộc
        $required = [];
        foreach ($tableSchema->columns as $column) {
            if ($column->isPrimaryKey && $column->autoIncrement) {
                continue;
            }

            if (!$column->allowNull && $column->defaultValue === null) {
                $required[] = $column->name;
            }
        }

        if ($required) {
            $rules[] = [$required, 'required'];
        }

        // Rule cho các kiểu dữ liệu
        foreach ($tableSchema->columns as $column) {
            if ($column->isPrimaryKey && $column->autoIncrement) {
                continue;
            }

            switch ($column->type) {
                case 'smallint':
                case 'integer':
                case 'bigint':
                    $rules[] = [[$column->name], 'integer'];
                    break;
                case 'decimal':
                case 'numeric':
                case 'real':
                case 'double precision':
                    $rules[] = [[$column->name], 'number'];
                    break;
                case 'character varying':
                case 'varchar':
                case 'character':
                case 'char':
                case 'text':
                    $rules[] = [[$column->name], 'string', 'max' => $column->size ?: null];
                    break;
                case 'boolean':
                    $rules[] = [[$column->name], 'boolean'];
                    break;
                case 'date':
                    $rules[] = [[$column->name], 'date', 'format' => 'php:Y-m-d'];
                    break;
                case 'timestamp without time zone':
                case 'timestamp with time zone':
                case 'timestamp':
                    $rules[] = [[$column->name], 'datetime', 'format' => 'php:Y-m-d H:i:s'];
                    break;
                case 'time without time zone':
                case 'time with time zone':
                case 'time':
                    $rules[] = [[$column->name], 'date', 'format' => 'php:H:i:s'];
                    break;
                case 'uuid':
                    $rules[] = [[$column->name], 'match', 'pattern' => '/^[a-f0-9\-]{36}$/'];
                    break;
                default:
                    $rules[] = [[$column->name], 'safe'];
                    break;
            }
        }

        return $rules;
    }
}
