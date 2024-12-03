<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Page;

use app\models\Menu;
use app\models\User;
use yii\filters\AccessControl;
use yii\data\Pagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\db\Schema;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

class PagesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays Manage Page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $menuId = Yii::$app->request->get('menuId');
        $searchTerm = Yii::$app->request->get('search', '');
        $pageSize = Yii::$app->request->get('pageSize', 10);

        $menu = Menu::findOne($menuId);
        if (!$menu) {
            throw new NotFoundHttpException('Không tìm thấy dữ liệu phù hợp.');
        }

        /** @var Page[] $pages */
        $pages = Page::find()
            ->where(['status' => 0, 'menu_id' => $menuId, 'deleted' => 0])
            ->orderBy(['position' => SORT_ASC, 'id' => SORT_DESC])
            ->all();

        if (!$pages) {
            throw new NotFoundHttpException('Không có Page nào ở đây.');
        }
        if (count($pages) == 1) {
            $page = $pages[0];
            if ($page->type == 'table') {
                $columns = Yii::$app->db->schema->getTableSchema($page->table_name)->columns;
                $columnNames = array_keys($columns);
                $query = (new Query())->from($page->table_name);

                if (!empty($searchTerm)) {
                    $condition = [];

                    foreach ($columnNames as $columnName) {
                        if ($columnName === 'hidden_id') {
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
                        'defaultOrder' => [
                            'hidden_id' => SORT_ASC,
                        ],
                        'attributes' => $columnNames,
                    ],
                ]);

                return $this->render('singlePageTable', [
                    'dataProvider' => $dataProvider,
                    'columns' => $columnNames,
                    'menu' => $menu,
                ]);
            } else {
                $filePath = Yii::getAlias('@runtime/richtext/' . $page->id . '.txt');
                if (!is_file($filePath)) {
                    throw new NotFoundHttpException('file not found');
                }
                $content = file_get_contents($filePath);
                return $this->render('singlePageRichText', [
                    'page' => $page,
                    'content' => $content,
                    'menu' => $menu,
                ]);
            }
        }
        return $this->render('multiPage', [
            'pages' => $pages,
            'menu' => $menu,
        ]);
    }

    /**
     * Load Page Data Action.
     *
     */
    public function actionLoadPageData()
    {
        $pageId = Yii::$app->request->get('pageId');
        $searchTerm = Yii::$app->request->get('search', '');
        $pageSize = Yii::$app->request->get('pageSize', 10);
        $page = Page::findOne($pageId);

        if ($page === null) {
            return 'No data';
        }

        if ($page->type === 'table') {
            $columns = Yii::$app->db->schema->getTableSchema($page->table_name)->columns;
            $columnNames = array_keys($columns);
            $query = (new Query())->from($page->table_name);

            if (!empty($searchTerm)) {
                $condition = [];

                foreach ($columnNames as $columnName) {
                    if ($columnName === 'hidden_id') {
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
                    'defaultOrder' => [
                        'hidden_id' => SORT_ASC,
                    ],
                    'attributes' => $columnNames,
                ],
            ]);

            return $this->renderAjax('_tableData', [
                'dataProvider' => $dataProvider,
                'columns' => $columnNames,
                'pageId' => $pageId,
            ]);
        } elseif ($page->type === 'richtext') {
            // Richtext Page
            $filePath = Yii::getAlias('@runtime/richtext/' . $pageId . '.txt');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderAjax('_richtextData', [
                'richtextTab' => $page,
                'content' => $content,
                'filePath' => $filePath,
            ]);
        }

        return 'No data';
    }

    /** 
     * Update RichtextData Action.
     *
     */
    public function actionSaveRichtext()
    {
        if (Yii::$app->request->isPost) {
            $pageId = Yii::$app->request->post('pageId');
            $content = Yii::$app->request->post('content');

            $filePath = Yii::getAlias('@runtime/richtext/' . $pageId . '.txt');
            try {
                file_put_contents($filePath, $content);
                return json_encode(['status' => 'success', 'message' => 'Nội dung đã được cập nhật thành công.']);
            } catch (\Exception $e) {
                return json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi cập nhật nội dung.']);
            }
        }
        return json_encode(['status' => 'error', 'message ' => 'Đã xảy ra lỗi khi cập nhật nội dung.']);
    }
    /** 
     * Download RichtextData Action.
     *
     */
    // public function actionDownload($pageId)
    // {
    //     $filePath = Yii::getAlias('@runtime/richtext/' . $pageId . '.txt');

    //     if (file_exists($filePath)) {
    //         return Yii::$app->response->sendFile($filePath);
    //     } else {
    //         throw new NotFoundHttpException('Không tìm thấy tệp tin.');
    //     }
    // }
    /** 
     * Update TableData Action.
     *
     */
    public function actionUpdateData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();

            // Lấy giá trị của ID
            $hidden_id = $postData['hidden_id'];

            // Lấy các cột cần cập nhật
            unset($postData['hidden_id']);  // Xoá 'hidden_id' khỏi mảng để chỉ còn lại các cột cần cập nhật

            // Xử lý dữ liệu, ví dụ cập nhật vào bảng `page_1`
            $tableName = $postData['tableName'];  // Lấy tên bảng từ dữ liệu gửi lên
            unset($postData['tableName']);  // Xoá 'tableName' khỏi mảng

            try {
                $db = Yii::$app->db;
                $escapedTableName = $db->quoteTableName($tableName);

                // Kiểm tra xem bảng có tồn tại không
                $tableSchema = $db->getTableSchema($tableName);
                if (!$tableSchema) {
                    throw new \Exception("Không tìm thấy thông tin bảng: $tableName");
                }

                // Kiểm tra và chỉ giữ lại các cột hợp lệ
                $validData = [];
                foreach ($postData as $column => $value) {
                    if (array_key_exists($column, $tableSchema->columns)) {
                        $validData[$column] = $value === '' ? null : $value;
                    }
                }

                if (empty($validData)) {
                    return $this->asJson(['success' => false, 'message' => 'Không có cột hợp lệ để cập nhật dữ liệu.']);
                }

                // Cập nhật dữ liệu vào bảng
                $result = $db->createCommand()->update($escapedTableName, $validData, 'hidden_id = :hidden_id', [':hidden_id' => $hidden_id])->execute();

                if ($result) {
                    // Trả về thông báo thành công
                    return $this->asJson([
                        'success' => true,
                        'message' => 'Cập nhật dữ liệu thành công.'
                    ]);
                } else {
                    // Trả về thông báo lỗi nếu không có thay đổi
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Không có thay đổi nào để cập nhật.'
                    ]);
                }
            } catch (\Exception $e) {
                // Trả về lỗi khi có ngoại lệ
                return $this->asJson([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
            }
        }

        // Trả về lỗi nếu không phải là POST
        return $this->asJson([
            'success' => false,
            'message' => 'Không có dữ liệu gửi đến.'
        ]);
    }

    /** 
     * Create TableData Action.
     *
     */
    public function actionAddData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Nhận tên bảng và dữ liệu từ request
        $tableName = Yii::$app->request->post('tableName');
        $data = Yii::$app->request->post();

        // Xóa `tableName` ra khỏi dữ liệu cần chèn
        unset($data['tableName']);

        if (empty($tableName) || empty($data)) {
            return $this->asJson(['success' => false, 'message' => 'Tên bảng hoặc dữ liệu không hợp lệ.']);
        }

        try {
            $db = Yii::$app->db;
            $escapedTableName = $db->quoteTableName($tableName);

            // Lấy thông tin schema và kiểm tra tồn tại bảng
            $tableSchema = $db->getTableSchema($tableName);
            if (!$tableSchema) {
                throw new \Exception("Không tìm thấy thông tin bảng: $tableName");
            }

            // Kiểm tra và chỉ giữ lại các cột hợp lệ
            $validData = [];
            foreach ($data as $column => $value) {
                if (array_key_exists($column, $tableSchema->columns)) {
                    $validData[$column] = $value === '' ? null : $value;
                }
            }

            if (empty($validData)) {
                return $this->asJson(['success' => false, 'message' => 'Không có cột hợp lệ để thêm dữ liệu.']);
            }

            // Lấy thông tin khóa chính
            $primaryKey = $tableSchema->primaryKey[0] ?? null;

            // Nếu khóa chính là auto-increment, loại bỏ khỏi dữ liệu
            if ($primaryKey && $tableSchema->columns[$primaryKey]->autoIncrement) {
                unset($validData[$primaryKey]);
            }

            // Thực hiện chèn dữ liệu
            $db->createCommand()->insert($escapedTableName, $validData)->execute();

            // Lấy tổng số bản ghi để tính số trang
            $totalRecords = $db->createCommand("SELECT COUNT(*) FROM $escapedTableName")->queryScalar();

            return $this->asJson([
                'success' => true,
                'message' => 'Thêm dữ liệu thành công.',
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi duplicate key hoặc null value
            if (
                strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false ||
                strpos($e->getMessage(), 'null value in column') !== false
            ) {
                try {
                    // Đồng bộ sequence của khóa chính
                    $sequenceName = $tableSchema->sequenceName;
                    if ($primaryKey && $sequenceName) {
                        $db->createCommand("
                            SELECT setval('$sequenceName', (SELECT MAX($primaryKey) FROM $escapedTableName))
                        ")->execute();
                    }

                    // Thử chèn lại dữ liệu
                    $db->createCommand()->insert($escapedTableName, $validData)->execute();

                    return $this->asJson(['success' => true, 'message' => 'Thêm dữ liệu thành công sau khi cập nhật sequence.']);
                } catch (\Exception $seqException) {
                    return $this->asJson(['success' => false, 'message' => 'Cập nhật sequence thất bại: ' . $seqException->getMessage()]);
                }
            }

            // Trả về lỗi khác
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    /**
     * Lấy tên sequence của bảng dựa trên thông tin schema PostgreSQL.
     */
    protected function getSequenceName($tableName)
    {
        $db = Yii::$app->db;
        $tableSchema = $db->getTableSchema($tableName);
        if (!$tableSchema || !$tableSchema->sequenceName) {
            throw new \Exception("Không tìm thấy sequence cho bảng: $tableName");
        }

        return $tableSchema->sequenceName;
    }

    /**
     * Xóa một bản ghi.
     */
    public function actionDeleteData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            $hidden_id = $postData['hidden_id'] ?? null; // Lấy ID từ dữ liệu gửi lên

            if (!$hidden_id) {
                return $this->asJson(['success' => false, 'message' => 'ID không hợp lệ.']);
            }

            // Lấy tên bảng và xử lý
            $tableName = $postData['tableName'] ?? null;  // Lấy tên bảng
            if (!$tableName) {
                return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
            }

            try {
                $db = Yii::$app->db;
                $escapedTableName = $db->quoteTableName($tableName);

                // Kiểm tra xem bảng có tồn tại không
                $tableSchema = $db->getTableSchema($tableName);
                if (!$tableSchema) {
                    throw new \Exception("Không tìm thấy bảng: $tableName");
                }

                // Thực hiện xóa bản ghi
                $result = $db->createCommand()->delete($escapedTableName, ['hidden_id' => $hidden_id])->execute();

                if ($result > 0) {
                    return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
                } else {
                    return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào để xóa.']);
                }
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }

    /**
     * Xóa nhiều bản ghi.
     */
    public function actionDeleteSelectedData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('ids'); // Lấy danh sách ID cần xóa

            if (empty($ids)) {
                return $this->asJson(['success' => false, 'message' => 'Danh sách ID không hợp lệ.']);
            }

            // Lấy tên bảng và xử lý
            $tableName = Yii::$app->request->post('tableName');
            if (!$tableName) {
                return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
            }

            try {
                $db = Yii::$app->db;
                $escapedTableName = $db->quoteTableName($tableName);

                // Kiểm tra xem bảng có tồn tại không
                $tableSchema = $db->getTableSchema($tableName);
                if (!$tableSchema) {
                    throw new \Exception("Không tìm thấy bảng: $tableName");
                }

                // Xóa các bản ghi đã chọn
                $result = $db->createCommand()->delete($escapedTableName, ['hidden_id' => $ids])->execute();

                if ($result > 0) {
                    return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
                } else {
                    return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào để xóa.']);
                }
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }

    /**
     * Import + Export Excel
     * 
     */
    public function actionImportExcel()
    {
        $file = $_FILES['import-excel-file'];
        $tableName = Yii::$app->request->post('tableName');

        if ($file['error'] === UPLOAD_ERR_OK) {
            $filePath = $file['tmp_name'];
            $data = iterator_to_array($this->parseExcel($filePath));

            if (empty($data)) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Tập tin không chứa dữ liệu.',
                ]);
            }

            $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);
            $columns = $tableSchema->columns;
            $expectedColumns = array_keys($columns);

            // Loại bỏ cột 'hidden_id' khỏi danh sách cột dự kiến
            $expectedColumns = array_filter($expectedColumns, fn($column) => strtolower($column) !== 'hidden_id');

            // Lấy header từ tệp Excel và loại bỏ 'hidden_id' nếu tồn tại
            $excelHeaders = $this->getColumnHeadersFromExcel($filePath);
            $excelHeaders = array_filter($excelHeaders, fn($header) => strtolower($header) !== 'hidden_id');

            // Kiểm tra tiêu đề cột
            if ($this->validateColumns($excelHeaders, $expectedColumns) === false) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Các tiêu đề cột trong tệp Excel không khớp với các cột trong cơ sở dữ liệu.',
                ]);
            }

            // Loại bỏ hàng đầu tiên (header) để chỉ lấy dữ liệu
            $data = array_slice($data, 1);

            // Loại bỏ cột 'id' khỏi dữ liệu
            $data = array_map(function ($row) use ($excelHeaders) {
                return array_filter($row, function ($key) use ($excelHeaders) {
                    return strtolower($key) !== 'hidden_id';
                }, ARRAY_FILTER_USE_KEY);
            }, $data);

            // Transaction
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Xóa toàn bộ dữ liệu trong bảng
                Yii::$app->db->createCommand()->delete($tableName)->execute();

                // Chèn dữ liệu mới
                $chunkSize = 1000;
                $rowIndex = 0;
                $totalRows = count($data);

                while ($rowIndex < $totalRows) {
                    $rowsToInsert = array_slice($data, $rowIndex, $chunkSize);
                    $rowIndex += count($rowsToInsert);

                    if (empty($rowsToInsert)) {
                        break;
                    }

                    // Chuẩn bị dữ liệu cho câu lệnh INSERT
                    $sql = sprintf(
                        'INSERT INTO "%s" ("%s") VALUES %s',
                        $tableName,
                        implode('", "', $excelHeaders),
                        implode(', ', array_map(function ($row) {
                            return '(' . implode(', ', array_map(function ($value) {
                                return is_null($value) ? 'NULL' : Yii::$app->db->quoteValue($value);
                            }, $row)) . ')';
                        }, $rowsToInsert))
                    );
                    Yii::$app->db->createCommand($sql)->execute();
                }

                $transaction->commit();
                return $this->asJson(['success' => true]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi trong quá trình nhập: ' . $e->getMessage(),
                ]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Không thể tải tệp Excel lên']);
    }


    // Validate Data Import
    private function isValidColumnType($value, $columnSchema)
    {
        if (is_null($value)) {
            return true; // Cho phép giá trị NULL
        }

        switch ($columnSchema->type) {
            case Schema::TYPE_INTEGER:
                return filter_var($value, FILTER_VALIDATE_INT) !== false;

            case Schema::TYPE_FLOAT:
            case Schema::TYPE_DOUBLE:
            case Schema::TYPE_DECIMAL:
                return is_numeric($value);

            case Schema::TYPE_BOOLEAN:
                return in_array($value, [0, 1, '0', '1', true, false], true);

            case Schema::TYPE_STRING:
            case Schema::TYPE_TEXT:
                return is_scalar($value);

            case Schema::TYPE_DATE:
                return strtotime($value) !== false && date('Y-m-d', strtotime($value)) === $value;

            case Schema::TYPE_DATETIME:
                return strtotime($value) !== false && date('Y-m-d H:i:s', strtotime($value)) === $value;

            default:
                return true;
        }
    }
    // Validate columns
    private function validateColumns($excelHeaders, $expectedColumns)
    {
        Yii::error("expectedColumns: " . print_r($expectedColumns, true), __METHOD__);
        Yii::error("excelHeaders: " . print_r($excelHeaders, true), __METHOD__);

        if (count($excelHeaders) !== count($expectedColumns)) {
            return false;
        }

        foreach ($excelHeaders as $header) {
            if (!in_array($header, $expectedColumns)) {
                return false;
            }
        }

        return true;
    }

    private function parseExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = $this->getColumnHeadersFromExcel($filePath);

        $rowIterator = $sheet->getRowIterator();
        // $rowIterator->next();

        foreach ($rowIterator as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            yield array_combine($headers, $rowData);
        }
    }

    private function getColumnHeadersFromExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $headerRow = $sheet->getRowIterator()->current();
        $headers = [];

        foreach ($headerRow->getCellIterator() as $cell) {
            $headers[] = (string) $cell->getValue();
        }

        return $headers;
    }
    public function actionExportExcel($format, $tableName)
    {
        // Lấy danh sách các cột của bảng, loại bỏ cột 'hidden_id'
        $columns = (new Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->andWhere(['<>', 'column_name', 'hidden_id'])  // Loại bỏ cột 'hidden_id'
            ->all();

        $columnNames = array_map(fn($column) => $column['column_name'], $columns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Thiết lập tiêu đề cột trong sheet Excel, loại bỏ cột 'hidden_id'
        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        // Định dạng tiêu đề cột
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ];
        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . '1')->applyFromArray($headerStyle);

        // Sắp xếp kết quả theo cột đầu tiên
        $query = (new Query())->from($tableName)
            ->orderBy($columnNames[0]);  // Sắp xếp theo cột đầu tiên

        $batchSize = 1000;
        $rowIndex = 2;

        // Xuất dữ liệu vào sheet
        foreach ($query->batch($batchSize) as $rows) {
            foreach ($rows as $row) {
                $columnIndex = 1;
                foreach ($columnNames as $column) {
                    // Loại bỏ dữ liệu của cột 'hidden_id'
                    if (isset($row[$column])) {
                        $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $row[$column]);
                    } else {
                        $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, '');
                    }
                    $columnIndex++;
                }
                $rowIndex++;
            }
        }

        // Định dạng các ô trong bảng
        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Thiết lập kích thước tự động cho các cột
        foreach (range('A', chr(64 + count($columnNames))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Tạo thư mục upload nếu chưa tồn tại
        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Lưu file Excel vào thư mục uploads
        $fileName = $tableName . '.' . $format;
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        // Trả về URL của file đã tạo
        $fileUrl = Yii::$app->urlManager->baseUrl . '/uploads/' . $fileName;

        return $this->asJson([
            'success' => true,
            'file_url' => $fileUrl
        ]);
    }



    public function getExportData($tableName)
    {
        $columns = Yii::$app->db->createCommand("DESCRIBE $tableName")->queryAll();
        $columnNames = array_map(fn($column) => $column['Field'], $columns);

        $data = [];
        foreach (Yii::$app->db->createCommand("SELECT * FROM $tableName")->query()->batch(1000) as $rows) {
            $data = array_merge($data, $rows);
        }

        return ['columns' => $columnNames, 'data' => $data];
    }

    public function actionDeleteExportFile()
    {
        $fileUrl = Yii::$app->request->post('file_url');
        $filePath = Yii::getAlias('@webroot') . parse_url($fileUrl, PHP_URL_PATH);

        if (file_exists($filePath)) {
            unlink($filePath);
            return $this->asJson(['success' => true]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'Không tìm thấy tập tin']);
        }
    }

    // Xuất Excel View Hiện tại
    public function actionExportExcelCurrent()
    {
        $format = Yii::$app->request->post('format');
        $tableName = Yii::$app->request->post('tableName');
        $visibleColumns = Yii::$app->request->post('visibleColumns');
        $tableData = Yii::$app->request->post('tableData');

        if (empty($tableData) || empty($visibleColumns)) {
            return $this->asJson(['success' => false, 'message' => 'Không có dữ liệu để xuất.']);
        }
        // Tạo file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cột (các cột hiển thị)
        $columnIndex = 1;
        foreach ($visibleColumns as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        // Áp dụng kiểu in đậm cho cột đầu tiên (dòng header)
        $sheet->getStyle('A1:' . chr(64 + count($visibleColumns)) . '1')->applyFromArray([
            'font' => ['bold' => true],  // Đặt font chữ in đậm cho header
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Đặt dữ liệu vào bảng
        $rowIndex = 2;
        foreach ($tableData as $row) {
            $columnIndex = 1;
            foreach ($visibleColumns as $column) {
                // Đảm bảo chỉ xuất các cột đã chọn
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, isset($row[$column]) ? $row[$column] : '');
                $columnIndex++;
            }
            $rowIndex++;
        }

        // Áp dụng style cho các ô dữ liệu
        $sheet->getStyle('A2:' . chr(64 + count($visibleColumns)) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Tự động điều chỉnh chiều rộng cột
        foreach (range('A', chr(64 + count($visibleColumns))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Tạo file và trả về đường dẫn
        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $tableName . '.' . $format;
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        $fileUrl = Yii::$app->urlManager->baseUrl . '/uploads/' . $fileName;

        return $this->asJson([
            'success' => true,
            'file_url' => $fileUrl
        ]);
    }

    // Xuất Template
    public function actionExportExcelHeader()
    {
        // Lấy tên bảng từ request
        $tableName = Yii::$app->request->post('tableName');
        Yii::error("tableName: " . $tableName);
        // Kiểm tra xem tên bảng có hợp lệ không
        // if (empty($tableName)) {
        //     return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
        // }

        // Truy vấn thông tin các cột từ cơ sở dữ liệu
        $columns = (new Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->all();

        // Kiểm tra nếu có cột
        if (empty($columns)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột nào trong bảng.']);
        }

        // Lấy tên các cột và loại bỏ cột 'hidden_id'
        $columnNames = array_filter(
            array_map(fn($column) => $column['column_name'], $columns),
            fn($columnName) => strtolower($columnName) !== 'hidden_id' // Loại bỏ cột 'hidden_id'
        );

        if (empty($columnNames)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột nào để xuất sau khi loại bỏ cột "id".']);
        }

        // Tạo đối tượng Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ghi các header columns vào sheet
        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        // Định dạng header (tùy chỉnh)
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ];
        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . '1')->applyFromArray($headerStyle);

        // Cấu hình kích thước cột tự động
        foreach (range('A', chr(64 + count($columnNames))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Tạo file Excel và lưu
        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $tableName . '-template.xlsx';
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        // Trả về URL của file đã tạo
        $fileUrl = Yii::$app->urlManager->baseUrl . '/uploads/' . $fileName;

        return $this->asJson([
            'success' => true,
            'file_url' => $fileUrl
        ]);
    }
}