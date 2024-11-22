<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PageForm extends Model
{
    public $pageName;
    public $tableName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pageName', 'tableName', 'type'], 'required', 'message' => 'Không được để trống.'],
            [['pageName', 'tableName'], 'string', 'min' => 3, 'message' => 'Tên phải có ít nhất 3 ký tự.'],
            ['pageName', 'validatePageName'],
            ['tableName', 'validateTableName'],
            ['type', 'in', 'range' => ['table', 'richtext']], // Kiểm tra giá trị hợp lệ cho type
        ];
    }
    public function attributeLabels()
    {
        return [
            'pageName' => 'Tên Page',
            'tableName' => 'Tên Bảng',
            'type' => 'Loại Trang', // Thêm label cho type
        ];
    }

    /**
     * Kiểm tra tính hợp lệ của Tên Page (ví dụ: kiểm tra xem tên đã tồn tại chưa)
     */
    public function validatePageName($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // Kiểm tra nếu Tên Page đã tồn tại trong cơ sở dữ liệu (giả sử có phương thức tìm kiếm)
            if ($this->isPageNameExists($this->pageName)) {
                $this->addError($attribute, 'Tên Page đã tồn tại.');
            }
        }
    }

    /**
     * Kiểm tra tính hợp lệ của Tên Bảng (ví dụ: kiểm tra xem tên đã tồn tại chưa)
     */
    public function validateTableName($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // Kiểm tra nếu Tên Table đã tồn tại trong cơ sở dữ liệu (giả sử có phương thức tìm kiếm)
            if ($this->isTableNameExists($this->tableName)) {
                $this->addError($attribute, 'Tên Bảng đã tồn tại.');
            }
        }
    }

    /**
     * Kiểm tra Tên Page đã tồn tại chưa (giả sử bạn có bảng pages trong DB)
     */
    private function isPageNameExists($pageName)
    {
        // Giả sử bạn có một model `Page` và muốn kiểm tra trong cơ sở dữ liệu
        return Page::find()->where(['name' => $pageName])->exists();
    }

    /**
     * Kiểm tra Tên Bảng đã tồn tại chưa (giả sử bạn có bảng tables trong DB)
     */
    private function isTableNameExists($tableName)
    {
        // Giả sử bạn có một model `Table` và muốn kiểm tra trong cơ sở dữ liệu
        return Yii::$app->db->createCommand("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = :tableName)")
            ->bindValue(':tableName', $tableName)
            ->queryScalar();
    }
}
