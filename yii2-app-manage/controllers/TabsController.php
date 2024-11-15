<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Exception;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\db\Schema;

class TabsController extends Controller
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
     * Displays Manage Tabs.
     *
     * @return string
     */
    public function actionTabView()
    {
        $userId = Yii::$app->user->id;
        $tabId = Yii::$app->request->get('tabId');

        $tab_item = Tab::find()
            ->where(['user_id' => $userId, 'deleted' => 0, 'id' => $tabId])
            ->one();
        $tableTabs = TableTab::find()->all();

        return $this->render('tab-view/index', [
            'tab_item' => $tab_item,
            'tableTabs' => $tableTabs,
        ]);
    }

    public function actionMenuView()
    {
        $userId = Yii::$app->user->id;

        $menuId = Yii::$app->request->get('menuId');

        $tabs = Tab::find()
            ->where(['user_id' => $userId, 'deleted' => 0, 'menu_id' => $menuId])
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all();;

        $tableTabs = TableTab::find()->all();

        return $this->render('menu-view/index', [
            'tabs' => $tabs,
            'tableTabs' => $tableTabs,
        ]);
    }


    /**
     * Load Tab Data Action.
     *
     */
    public function actionLoadTabData($tabId)
    {
        $tabId = Yii::$app->request->get('tabId');

        $tab = Tab::findOne($tabId);
        $userId = Yii::$app->user->id;

        // Retrieve search keyword if it exists

        $searchTerm = Yii::$app->request->get('search', '');
        $pageSize = intval(Yii::$app->request->get('pageSize', 10));
        $page = intval(Yii::$app->request->get('page', 0));


        if ($tab === null) {
            return 'No data';
        }

        $tabType = $tab->tab_type;

        if ($tabType === 'table') {
            // Table Tab
            $tableTab = TableTab::find()->where(['tab_id' => $tabId])->one();
            $tableName = $tableTab ? $tableTab->table_name : null;

            if ($tableName) {
                $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                $columnNames = array_keys($columns);

                $query = (new \yii\db\Query())->from($tableName);

                if (!empty($searchTerm)) {
                    $query->where(['or', ...array_map(fn($c) => ['like', $c, $searchTerm], $columnNames)]);
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
                ]);
            }
        } elseif ($tabType === 'richtext') {
            // Richtext Tab
            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.txt');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderPartial('_richtextData', [
                'richtextTab' => $tab,
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
            $tabId = Yii::$app->request->post('tabId');
            $content = Yii::$app->request->post('content');

            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.txt');
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
    public function actionDownload($tab_id)
    {
        $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.txt');

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('Không tìm thấy tệp tin.');
        }
    }
    /** 
     * Update TableData Action.
     *
     */
    public function actionUpdateData()
    {
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');
        $originalValues = Yii::$app->request->post('originalValues');

        if (isset($originalValues['id'])) {
            $whereCondition = "`id` = :original_id";
        } else {
            $whereCondition = '';
        }

        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "`$column` = :$column";
        }
        $setCondition = implode(", ", $setClause);

        $sql = "UPDATE `$tableName` SET $setCondition" . ($whereCondition ? " WHERE $whereCondition" : "");
        $command = Yii::$app->db->createCommand($sql);

        foreach ($data as $column => $value) {
            $command->bindValue(":$column", $value === '' ? null : $value);
        }
        if (isset($originalValues['id'])) {
            $command->bindValue(":original_id", $originalValues['id']);
        }

        try {
            $command->execute();
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

        $validData = [];
        foreach ($data as $column => $value) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column) || is_numeric($column)) {
                $validData[$column] = $value === '' ? null : $value;
            }
        }

        if (empty($validData)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột hợp lệ để thêm dữ liệu.']);
        }

        $sql = "INSERT INTO `$tableName` (`" . implode("`, `", array_keys($validData)) . "`) VALUES (:" . implode(", :", array_keys($validData)) . ")";
        $command = Yii::$app->db->createCommand($sql);

        foreach ($validData as $column => $value) {
            $command->bindValue(":$column", $value);
        }

        try {
            $command->execute();

            $countSql = "SELECT COUNT(*) FROM `$tableName`";
            $totalRecords = Yii::$app->db->createCommand($countSql)->queryScalar();

            $pageSize = 10;
            $totalPages = ceil($totalRecords / $pageSize);

            return $this->asJson([
                'success' => true,
                'totalPages' => $totalPages,
                'redirect' => '/tabs',
            ]);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
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
                        $tempConditions[] = "`$column` IS NULL";
                    } else {
                        $tempConditions[] = "`$column` = '" . addslashes($value) . "'";
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
            $sql = "DELETE FROM `$table` WHERE ";
            $columns = array_keys($conditions[0]);
            $nullConditions = [];

            foreach ($columns as $column) {
                $nullConditions[] = "`$column` IS NULL";
            }

            $sql .= implode(' AND ', $nullConditions);
        } else {
            $sql = "DELETE FROM `$table` WHERE " . implode(' OR ', $whereConditions);
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
                    $existingIds = Yii::$app->db->createCommand("SELECT `$primaryKey` FROM {$tableName} WHERE `$primaryKey` IN (" . implode(',', $primaryKeyValues) . ")")
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
                                return "`$column` = VALUES(`$column`)";
                            }, array_keys($rowData)));

                            $sql = "INSERT INTO {$tableName} (`$columnsList`) VALUES ($valuesList) ON DUPLICATE KEY UPDATE $updateList";
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
        $columns = Yii::$app->db->createCommand("DESCRIBE `$tableName`")->queryAll();
        $columnNames = array_map(fn($column) => $column['Field'], $columns);

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

        $query = (new \yii\db\Query())->from($tableName);
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

        // Border
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
        $columns = Yii::$app->db->createCommand("DESCRIBE `$tableName`")->queryAll();
        $columnNames = array_map(fn($column) => $column['Field'], $columns);

        $data = [];
        foreach (Yii::$app->db->createCommand("SELECT * FROM `$tableName`")->query()->batch(1000) as $rows) {
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
}
