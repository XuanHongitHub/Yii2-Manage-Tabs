<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Page;

use app\models\Menu;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Exception;
use yii\data\Pagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\db\Schema;
use yii\web\NotFoundHttpException;
use yii\db\Query;

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

        $menu = Menu::findOne($menuId);

        if ($menu) {
            $pages = Page::find()
                ->where(['status' => 0, 'menu_id' => $menuId])
                ->orderBy(['position' => SORT_ASC, 'id' => SORT_DESC])
                ->all();


            return $this->render('menu', [
                'pages' => $pages,
            ]);
        }

        throw new NotFoundHttpException('Không tìm thấy dữ liệu phù hợp.');
    }



    /**
     * Load Page Data Action.
     *
     */
    public function actionLoadPageData($pageId)
    {
        $pageId = Yii::$app->request->get('pageId');

        $pageTab = Page::findOne($pageId);
        $userId = Yii::$app->user->id;

        // Retrieve search keyword if it exists

        $searchTerm = Yii::$app->request->get('search', '');
        $pageSize = intval(Yii::$app->request->get('pageSize', 10));
        $page = intval(Yii::$app->request->get('page', 0));


        if ($page === null) {
            return 'No data';
        }

        $pageType = $pageTab->type;

        if ($pageType === 'table') {

            $tableName = $pageTab ? $pageTab->table_name : null;

            if ($tableName) {
                $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                $columnNames = array_keys($columns);

                $query = (new Query())->from($tableName);

                if (!empty($searchTerm)) {
                    $query->where(['or', ...array_map(function ($c) use ($searchTerm) {
                        return ['ilike', "LOWER(unaccent(CAST($c AS TEXT)))", strtolower($searchTerm)];
                    }, $columnNames)]);
                }


                $totalCount = $query->count();

                $pagination = new Pagination([
                    'defaultPageSize' => $pageSize,
                    'pageSize' => $pageSize,
                    'totalCount' => $totalCount,
                    'page' => Yii::$app->request->get('page', 0)
                ]);

                $query->offset($page * $pageSize)
                    ->limit($pageSize);

                $data = $query->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();

                return $this->renderPartial('_tableData', [
                    'columns' => $columns,
                    'data' => $data,
                    'tableName' => $tableName,
                    'pagination' => $pagination,
                    'totalCount' => $totalCount,
                    'pageSize' => $pageSize,
                    'pageId' => $pageId,
                ]);
            }
        } elseif ($pageType === 'richtext') {
            // Richtext Page
            $filePath = Yii::getAlias('@runtime/richtext/' . $pageId . '.txt');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderPartial('_richtextData', [
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
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');
        $originalValues = Yii::$app->request->post('originalValues');

        $primaryKeyColumn = Yii::$app->db->schema->getTableSchema($tableName)->primaryKey[0];

        if (!isset($data[$primaryKeyColumn]) || !preg_match('/^[a-zA-Z0-9_]+$/', $data[$primaryKeyColumn])) {
            return $this->asJson(['success' => false, 'message' => 'Khóa chính không hợp lệ.']);
        }

        $whereCondition = [$primaryKeyColumn => $originalValues[$primaryKeyColumn]];

        Yii::error("DATA: " . $data);
        try {
            Yii::$app->db->createCommand()
                ->update($tableName, $data, $whereCondition)
                ->execute();

            return $this->asJson(['success' => true]);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    /** 
     * Create TableData Action.
     *
     */
    public function actionAddData()
    {
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');

        // Kiểm tra và chỉ giữ lại các cột hợp lệ
        $validData = [];
        foreach ($data as $column => $value) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                $validData[$column] = $value === '' ? null : $value;
            }
        }

        if (empty($validData)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột hợp lệ để thêm dữ liệu.']);
        }

        try {
            $db = Yii::$app->db;
            $escapedTableName = $db->quoteTableName($tableName);

            // Lấy thông tin schema và khóa chính của bảng
            $tableSchema = $db->getTableSchema($tableName);
            if (!$tableSchema) {
                throw new \Exception("Không tìm thấy thông tin bảng: $tableName");
            }

            // Lấy tên cột khóa chính
            $primaryKey = $tableSchema->primaryKey[0] ?? null;

            // Nếu khóa chính là auto-increment, loại bỏ khỏi dữ liệu
            if ($primaryKey && $tableSchema->columns[$primaryKey]->autoIncrement) {
                unset($validData[$primaryKey]);
            }

            // Thực hiện chèn dữ liệu
            $db->createCommand()->insert($escapedTableName, $validData)->execute();

            $totalRecords = $db->createCommand("SELECT COUNT(*) FROM $escapedTableName")->queryScalar();

            $pageSize = 10;
            $totalPages = ceil($totalRecords / $pageSize);

            return $this->asJson([
                'success' => true,
                'totalPages' => $totalPages,
                'redirect' => '/pages',
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
     * Delete TableData Action.
     *
     */
    public function actionDeleteData()
    {
        $postData = Yii::$app->request->post();

        $table = $postData['table'];
        $conditions = isset($postData['conditions']) ? $postData['conditions'] : [];

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
        }

        $whereConditions = [];

        foreach ($conditions as $condition) {
            $tempConditions = [];

            foreach ($condition as $column => $value) {

                if (preg_match('/^[a-zA-Z_0-9][a-zA-Z0-9_]*$/', $column)) {
                    if ($value === '') {
                        $tempConditions[] = "$column IS NULL";
                    } else {
                        $tempConditions[] = "$column = '" . addslashes($value) . "'";
                    }
                } else {
                    Yii::error("Invalid column name: $column", __METHOD__);
                }
            }

            if (!empty($tempConditions)) {
                $whereConditions[] = '(' . implode(' AND ', $tempConditions) . ')';
            } else {
                Yii::error("No valid conditions for this set: " . json_encode($condition), __METHOD__);
            }
        }


        if (empty($whereConditions)) {
            $sql = "DELETE FROM $table WHERE ";
            $columns = array_keys($conditions[0]);
            $nullConditions = [];

            foreach ($columns as $column) {
                $nullConditions[] = "$column IS NULL";
            }

            $sql .= implode(' AND ', $nullConditions);
        } else {
            $sql = "DELETE FROM $table WHERE " . implode(' OR ', $whereConditions);
        }

        try {
            Yii::$app->db->createCommand($sql)->execute();
            return $this->asJson(['success' => true, 'message' => 'Successfully!']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    /**
     * Import + Export Excel
     * 
     */
    public function actionImportExcel()
    {
        $file = $_FILES['import-excel-file'];
        $tableName = Yii::$app->request->post('tableName');
        $removeId = Yii::$app->request->post('removeId');

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

            $excelHeaders = $this->getColumnHeadersFromExcel($filePath);

            if ($this->validateColumns($excelHeaders, $expectedColumns) === false) {
                $errorMessage = "\nCác tiêu đề cột trong tệp Excel không khớp với các cột cơ sở dữ liệu. Vui lòng xem lại các chi tiết sau:\n\n";

                $errorMessage .= "Cột tệp trong tệp Excel:\n" . "<div class='d-flex gap-3'>" . implode("", array_map(function ($header) {
                    return "<div class='p-2 text-danger'>" . htmlspecialchars($header) . "</div>";
                }, $excelHeaders)) . "</div>\n\n";

                $errorMessage .= "Các cột dự kiến ​​trong bảng:\n" . "<div class='d-flex gap-3'>" . implode("", array_map(function ($column) {
                    return "<div class='p-2 text-success'>" . htmlspecialchars($column) . "</div>";
                }, $expectedColumns)) . "</div>\n\n";

                return $this->asJson([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }

            $data = array_slice($data, 1);

            $primaryKey = $tableSchema->primaryKey[0];
            $duplicateIds = [];

            if (!$removeId && isset($columns[$primaryKey])) {
                $primaryKeyValues = array_filter(array_column($data, $primaryKey));
                if (!empty($primaryKeyValues)) {
                    $existingIds = Yii::$app->db->createCommand("SELECT $primaryKey FROM {$tableName} WHERE $primaryKey IN (" . implode(',', $primaryKeyValues) . ")")
                        ->queryColumn();

                    foreach ($data as $key => $row) {
                        if (in_array($row[$primaryKey], $existingIds)) {
                            $duplicateIds[] = $row[$primaryKey];
                            unset($data[$key]);  // 
                        }
                    }

                    if (!empty($duplicateIds)) {
                        return $this->asJson([
                            'success' => false,
                            'duplicate' => true,
                            'message' => 'Dữ liệu có PK(s) trùng lặp: ' . implode(', ', $duplicateIds) . '. Những hàng có PK(s) trên sẽ bị ghi đè.'
                        ]);
                    }
                }
            }

            // Transaction
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $chunkSize = 1000;
                $rowIndex = 0;
                $totalRows = count($data);
                $errors = [];

                while ($rowIndex < $totalRows) {
                    $rowsToInsert = array_slice($data, $rowIndex, $chunkSize);
                    $rowIndex += count($rowsToInsert);

                    if (empty($rowsToInsert)) {
                        break;
                    }

                    // INSERT ... ON DUPLICATE KEY UPDATE
                    foreach ($rowsToInsert as $index => $row) {
                        $currentRowIndex = $rowIndex - count($rowsToInsert) + $index + 2;
                        $rowData = [];
                        $rowErrors = [];

                        // Update
                        foreach ($expectedColumns as $column) {
                            $columnSchema = $columns[$column];
                            $value = isset($row[$column]) ? $row[$column] : null;

                            if (!$this->isValidColumnType($value, $columnSchema)) {
                                $rowErrors[] = "Giá trị '<strong class=\"txt-danger\">{$value}</strong>' cho cột '<strong class=\"txt-danger\">{$column}</strong>' không hợp lệ.  
                                Loại dữ liệu dự kiến: <strong class=\"text-success\">" . strtoupper($columnSchema->type) . "</strong> nhưng đã nhận được: <strong class=\"txt-danger\">" . strtoupper(gettype($value)) . "</strong>";
                            }

                            $rowData[$column] = $value;
                        }

                        if (!empty($rowErrors)) {
                            $errors[] = "Error at row {$currentRowIndex}:\n" . implode("\n\n", $rowErrors);
                        } else {
                            $columnsList = implode('`, `', array_keys($rowData));
                            $valuesList = implode(', ', array_map(function ($value) {
                                return is_null($value) ? 'NULL' : Yii::$app->db->quoteValue($value);
                            }, $rowData));

                            $updateList = implode(', ', array_map(function ($column) {
                                return "$column = VALUES($column)";
                            }, array_keys($rowData)));

                            $sql = "INSERT INTO {$tableName} ($columnsList) VALUES ($valuesList) ON DUPLICATE KEY UPDATE $updateList";
                            Yii::$app->db->createCommand($sql)->execute();
                        }
                    }
                }

                if (!empty($errors)) {
                    $errorMessages = [];
                    foreach ($errors as $error) {
                        $errorMessages[] = "<strong class=\"\">{$error}</strong>";
                    }
                    throw new \Exception("Đã tìm thấy lỗi trong quá trình nhập: \n\n" . implode("\n\n", $errorMessages));
                }

                // Commit transaction 
                $transaction->commit();

                return $this->asJson(['success' => true]);
            } catch (\Exception $e) {
                // Rollback transaction 
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
        $columns = (new Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->all();

        $columnNames = array_map(fn($column) => $column['column_name'], $columns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

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

        $query = (new Query())->from($tableName);
        $batchSize = 1000;
        $rowIndex = 2;

        foreach ($query->batch($batchSize) as $rows) {
            foreach ($rows as $row) {
                $columnIndex = 1;
                foreach ($row as $cell) {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $cell);
                    $columnIndex++;
                }
                $rowIndex++;
            }
        }

        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        foreach (range('A', chr(64 + count($columnNames))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

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
        // Nhận dữ liệu bảng từ client (dữ liệu đã lọc/sắp xếp)
        $tableName = Yii::$app->request->post('tableName');
        $format = Yii::$app->request->post('format');
        $tableData = Yii::$app->request->post('tableData');


        // Kiểm tra nếu không có dữ liệu, trả về lỗi
        if (empty($tableData)) {
            return $this->asJson(['success' => false, 'message' => 'Không có dữ liệu để xuất']);
        }

        // Lấy tên cột từ cơ sở dữ liệu (dùng lại query như trước để lấy cột)
        $columns = (new Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->all();

        $columnNames = array_map(fn($column) => $column['column_name'], $columns);

        // Tạo đối tượng Spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Điền tên cột vào header
        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        // Định dạng header (cột tên)
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

        // Điền dữ liệu từ bảng hiện tại (dữ liệu đã lọc/sắp xếp)
        $rowIndex = 2;
        foreach ($tableData as $row) {
            $columnIndex = 1;
            foreach ($row as $cell) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $cell);
                $columnIndex++;
            }
            $rowIndex++;
        }

        // Áp dụng border cho toàn bộ bảng dữ liệu
        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Tự động điều chỉnh chiều rộng các cột
        foreach (range('A', chr(64 + count($columnNames))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Đảm bảo thư mục uploads tồn tại
        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Lưu file Excel vào thư mục uploads
        $fileName = $tableName . '.' . $format;
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        // Tạo URL tải file
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

        // Kiểm tra xem tên bảng có hợp lệ không
        if (empty($tableName)) {
            return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
        }

        // Truy vấn thông tin các cột từ cơ sở dữ liệu
        $columns = (new \yii\db\Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->all();

        // Kiểm tra nếu có cột
        if (empty($columns)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột nào trong bảng.']);
        }

        // Lấy tên các cột vào mảng
        $columnNames = array_map(fn($column) => $column['column_name'], $columns);

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