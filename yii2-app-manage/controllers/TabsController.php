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
                        'roles' => ['@'],  // Yêu cầu người dùng đã đăng nhập
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],  // Từ chối người dùng chưa đăng nhập
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
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $tabs = Tab::find()
            ->where(['user_id' => $userId])
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->all();

        $tableTabs = TableTab::find()->all();

        return $this->render('index', [
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

        if ($tab === null) {
            return 'No data';
        }

        $tabType = $tab->tab_type;

        if ($tabType === 'table') {
            // Table Tab
            $tableTab = TableTab::find()->where(['tab_id' => $tabId])->one();
            $tableName = $tableTab ? $tableTab->table_name : null;

            if ($tableName) {
                // Get column names
                $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                $columnNames = array_keys($columns);

                $query = (new \yii\db\Query())->from($tableName);

                // If search term exists, apply search on all columns
                if (!empty($searchTerm)) {
                    $query->where(['or', ...array_map(fn($c) => ['like', $c, $searchTerm], $columnNames)]);
                }

                // Get total count for pagination
                $totalCount = $query->count();

                $pagination = new Pagination([
                    'defaultPageSize' => 10,
                    'totalCount' => $totalCount,
                    'page' => Yii::$app->request->get('page', 0)
                ]);

                // Get the data with limit and offset for pagination
                $data = $query->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();

                return $this->renderPartial('_tableData', [
                    'columns' => $columns,
                    'data' => $data,
                    'tableName' => $tableName,
                    'pagination' => $pagination,
                ]);
            }
        } elseif ($tabType === 'richtext') {
            // Richtext Tab
            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.rtf');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderPartial('_richtextData', [
                'richtextTab' => $tab,
                'content' => $content,
                'filePath' => $filePath,
            ]);
        }

        return 'No data';
    }
    public function actionSearchTabData($tabId)
    {
        $tabId = Yii::$app->request->get('tabId');

        $tab = Tab::findOne($tabId);
        $userId = Yii::$app->user->id;

        // Retrieve search keyword if it exists

        $searchTerm = Yii::$app->request->get('search', '');

        if ($tab === null) {
            return 'No data';
        }

        $tabType = $tab->tab_type;

        if ($tabType === 'table') {
            // Table Tab
            $tableTab = TableTab::find()->where(['tab_id' => $tabId])->one();
            $tableName = $tableTab ? $tableTab->table_name : null;

            if ($tableName) {
                // Get column names
                $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                $columnNames = array_keys($columns);

                $query = (new \yii\db\Query())->from($tableName);

                // If search term exists, apply search on all columns
                if (!empty($searchTerm)) {
                    $query->where(['or', ...array_map(fn($c) => ['like', $c, $searchTerm], $columnNames)]);
                }

                // Get total count for pagination
                $totalCount = $query->count();

                $pagination = new Pagination([
                    'defaultPageSize' => 10,
                    'totalCount' => $totalCount,
                    'page' => Yii::$app->request->get('page', 1) - 1,
                ]);

                // Get the data with limit and offset for pagination
                $data = $query->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();

                return $this->render('_tablePage', [
                    'columns' => $columns,
                    'data' => $data,
                    'tableName' => $tableName,
                    'pagination' => $pagination,
                ]);
            }
        } elseif ($tabType === 'richtext') {
            // Richtext Tab
            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.rtf');
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

            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.rtf');
            try {
                file_put_contents($filePath, $content);
                return json_encode(['status' => 'success', 'message' => 'Content has been updated successfully.']);
            } catch (\Exception $e) {
                return json_encode(['status' => 'error', 'message' => 'An error occurred while updating the content.']);
            }
        }
        return json_encode(['status' => 'error', 'message ' => 'Invalid request.']);
    }
    /** 
     * Download RichtextData Action.
     *
     */
    public function actionDownload($tab_id)
    {
        $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.rtf');

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('File not found.');
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


        Yii::error("Data: " . $data);
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
     * Delete Tab Action.
     *
     */
    public function actionDeleteTab()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['tabId'])) {
            $tabId = $postData['tabId'];

            $affectedRows = Tab::updateAll(
                ['deleted' => 1],
                ['id' => $tabId]
            );

            if ($affectedRows > 0) {
                return $this->asJson(['success' => true, 'message' => 'Soft delete successful.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'No records updated.']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'Missing tabId.']);
        }
    }
    /** 
     * Update Restore Action.
     *
     */
    public function actionRestoreTab()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['tabId'])) {
            $tabId = $postData['tabId'];

            $affectedRows = Tab::updateAll(
                ['deleted' => 0],
                ['id' => $tabId]
            );

            if ($affectedRows > 0) {
                return $this->asJson(['success' => true, 'message' => 'Restore successful.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'No records updated.']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'Missing tabId.']);
        }
    }
    /** 
     * Delete Permanently Tab Action.
     *
     */
    public function actionDeletePermanentlyTab()
    {
        $postData = Yii::$app->request->post();

        $tabId = $postData['tabId'];

        $tab = Tab::find()->where(['id' => $tabId])->one();

        if (!$tab) {
            return $this->asJson(['success' => false, 'message' => 'Tab does not exist.']);
        }
        if ($tab->tab_type == 'table') {
            $tableName = $postData['tableName'];

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                return $this->asJson(['success' => false, ' message' => 'Invalid table name.']);
            }
            $sql = "DROP TABLE IF EXISTS `$tableName`";

            try {
                Yii::$app->db->createCommand($sql)->execute();

                $tableTabTable = 'table_tab';
                $deleteTabSql = "DELETE FROM `$tableTabTable` WHERE `tab_id` = :tabId";
                Yii::$app->db->createCommand($deleteTabSql)->bindValue(':tabId', $tabId)->execute();

                $tabTable = 'tab';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :tabId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':tabId', $tabId)->execute();

                return $this->asJson(['success' => true, 'message' => 'Table and data were successfully deleted.']);
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        } elseif ($tab->tab_type == 'richtext') {
            try {
                $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.rtf');

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $tabTable = 'tab';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :tabId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':tabId', $tabId)->execute();

                return $this->asJson(['success' => true, 'message' => 'Richtext data was successfully deleted.']);
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        return $this->asJson(['success' => false, 'message' => 'Invalid tab type.']);
    }
    /** 
     * Update Postion Action.
     *
     */
    public function actionUpdateSortOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tabs = Yii::$app->request->post('tabs');

        if ($tabs) {
            foreach ($tabs as $tab) {
                $model = Tab::findOne($tab['id']);
                if ($model) {
                    $model->position = $tab['position'];
                    if (!$model->save()) {
                        return [
                            'success' => false,
                            'message' => 'Unable to save tab with ID: ' . $tab['id'],
                        ];
                    }
                }
            }
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Invalid data.'
        ];
    }
    /** 
     * Update Show/Hide Tab Action.
     *
     */
    public function actionUpdateHideStatus()
    {
        $hideStatus = Yii::$app->request->post('hideStatus', []);

        foreach ($hideStatus as $tabId => $status) {
            $tab = Tab::findOne($tabId);
            if ($tab) {
                $tab->deleted = $status;
                $tab->save();
            }
        }

        return $this->asJson(['success' => true]);
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
            $data = $this->parseExcel($filePath); // Hàm đọc dữ liệu từ file Excel

            $duplicateIds = [];
            if (!$removeId) { // Kiểm tra trùng lặp nếu không yêu cầu xóa cột 'id'
                foreach ($data as $row) {
                    $exists = Yii::$app->db->createCommand("SELECT COUNT(*) FROM {$tableName} WHERE id = :id")
                        ->bindValue(':id', $row['id'])
                        ->queryScalar();

                    if ($exists) {
                        $duplicateIds[] = $row['id'];
                    }
                }

                if (!empty($duplicateIds)) {
                    return $this->asJson([
                        'success' => false,
                        'duplicate' => true,
                        'message' => 'Data with duplicate id: ' . implode(', ', $duplicateIds),
                    ]);
                }
            } else {
                // Loại bỏ cột 'id' khỏi tất cả các hàng nếu removeId được bật
                foreach ($data as &$row) {
                    unset($row['id']);
                }
            }

            // Thực hiện import dữ liệu vào bảng
            foreach ($data as $row) {
                Yii::$app->db->createCommand()->insert($tableName, $row)->execute();
            }

            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false, 'message' => 'Unable to upload Excel file']);
    }


    private function parseExcel($filePath)
    {
        // Sử dụng PhpSpreadsheet để load file Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Lấy dữ liệu từ sheet và lưu thành mảng
        $data = [];
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            if ($rowIndex == 1) {
                // Bỏ qua dòng tiêu đề
                continue;
            }

            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Thêm vào mảng dữ liệu
            $data[] = array_combine($this->getColumnHeaders($sheet), $rowData);
        }

        return $data;
    }

    private function getColumnHeaders($sheet)
    {
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1)->current()->getCellIterator() as $cell) {
            $headers[] = $cell->getValue();
        }
        return $headers;
    }

    public function actionExportExcel($format, $tableName)
    {
        $data = $this->getExportData($tableName);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columns = array_keys($data[0]);
        $columnIndex = 1;
        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:' . chr(64 + count($columns)) . '1')->applyFromArray($headerStyle);

        $rowIndex = 2;
        foreach ($data as $row) {
            $columnIndex = 1;
            foreach ($row as $cell) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $cell);
                $sheet->getStyleByColumnAndRow($columnIndex, $rowIndex)
                    ->getAlignment()->setWrapText(true);
                $columnIndex++;
            }
            $rowIndex++;
        }

        $sheet->getStyle('A1:' . chr(64 + count($columns)) . ($rowIndex - 1))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

        foreach (range('A', chr(64 + count($columns))) as $columnID) {
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
        return Yii::$app->db->createCommand("SELECT * FROM `$tableName` ")->queryAll();
    }

    public function actionDeleteExportFile()
    {
        $fileUrl = Yii::$app->request->post('file_url');
        $filePath = Yii::getAlias('@webroot') . parse_url($fileUrl, PHP_URL_PATH);

        if (file_exists($filePath)) {
            unlink($filePath); // Xóa tệp
            return $this->asJson(['success' => true]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'File not found']);
        }
    }

}