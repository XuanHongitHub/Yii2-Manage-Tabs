<?php

namespace app\controllers;

use app\models\BaseModel;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Page;

use app\models\Menu;
use app\models\User;
use Exception;
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save-richtext' => ['post', 'put'],
                    'delete-data' => ['post'],
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
                        'defaultOrder' => [
                            BaseModel::HIDDEN_ID_KEY => SORT_ASC,
                        ],
                        'attributes' => $columnNames,
                    ],
                ]);

                return $this->render('singlePageTable', [
                    'dataProvider' => $dataProvider,
                    'columns' => $columnNames,
                    'menu' => $menu,
                    'pageId' => $page->id,
                ]);
            } elseif ($page->type === 'richtext') {
                $content = $page->content;

                return $this->render('singlePageRichText', [
                    'page' => $page,
                    'content' => $content,
                    'menu' => $menu,
                    'pageId' => $page->id,
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
                    'defaultOrder' => [
                        BaseModel::HIDDEN_ID_KEY => SORT_ASC,
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
            $content = $page->content;

            return $this->renderAjax('_richtextData', [
                'page' => $page,
                'content' => $content,
                'pageId' => $page->id,
            ]);
        }

        return 'No data';
    }

    /** 
     * Update RichtextData Action.
     *
     */
    public function actionSaveRichText()
    {
        if (Yii::$app->request->isPost) {
            $pageId = Yii::$app->request->post('pageId');
            $content = Yii::$app->request->post('content');

            try {
                $page = $this->loadPage($pageId);
            } catch (Exception $e) {
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }

            $page->content = $content;

            if ($page->save()) {
                return json_encode(['status' => 'success', 'message' => 'Nội dung đã được cập nhật thành công.']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi cập nhật nội dung.']);
            }
        }

        return json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
    }

    /** 
     * Update TableData Action.
     *
     */
    public function actionUpdateData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        }

        $pageId = Yii::$app->request->post('pageId');
        $formData = Yii::$app->request->post('data');

        if (!$pageId || !$formData) {
            return $this->asJson(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        }

        parse_str($formData, $data);

        $id = $data[BaseModel::HIDDEN_ID_KEY] ?? null; // Lấy ID từ dữ liệu
        if (!$id) {
            return $this->asJson(['success' => false, 'message' => 'ID không được cung cấp.']);
        }

        unset($data[BaseModel::HIDDEN_ID_KEY]); // Xóa ID để không ghi đè

        try {
            $page = $this->loadPage($pageId); // Load thông tin bảng
            $tableName = $page->table_name;

            $model = BaseModel::withTable($tableName)->findOne([BaseModel::HIDDEN_ID_KEY => $id]);
            if (!$model) {
                return $this->asJson(['success' => false, 'message' => 'Không tìm thấy bản ghi.']);
            }

            // Load và cập nhật dữ liệu vào model
            $model->load($data, '');
            if ($model->save()) {
                return $this->asJson(['success' => true, 'message' => 'Cập nhật dữ liệu thành công.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Lỗi khi lưu dữ liệu: ', 'errors' => $model->errors]);
            }
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /** 
     * Create TableData Action.
     *
     */
    public function actionAddData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Lấy pageId từ dữ liệu POST
        $pageId = Yii::$app->request->post('pageId');

        try {
            $page = $this->loadPage($pageId); // Lấy thông tin trang
        } catch (Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }

        $tableName = $page->table_name;

        $model = BaseModel::withTable($tableName);

        $formData = Yii::$app->request->post('data');

        parse_str($formData, $data);

        $model->load(array_merge(Yii::$app->request->post(), $data), '');

        Yii::error($model->attributes);
        if ($model->save()) {
            return $this->asJson(['success' => true, 'message' => 'Thêm dữ liệu thành công.']);
        } else {
            return $this->asJson(['success' => false, 'message' => 'Lỗi khi thêm dữ liệu: ', 'errors' => $model->errors]);
        }
    }


    /**
     * Summary of loadPage
     * @param mixed $pageId
     * @throws \Exception
     * @return Page
     */
    private function loadPage($pageId)
    {
        $page = Page::findOne(['id' => $pageId]);
        if (!$page) {
            throw new \Exception('');
        }
        return $page;
    }


    /**
     * Xóa một bản ghi.
     */
    public function actionDeleteData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pageId = Yii::$app->request->post('pageId');
        $data = Yii::$app->request->post();
        $id = $data[BaseModel::HIDDEN_ID_KEY] ?? null;

        try {
            $page = $this->loadPage($pageId);
        } catch (Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
        $tableName = $page->table_name;

        $model = BaseModel::withTable($tableName)->findOne([BaseModel::HIDDEN_ID_KEY => $id]);

        if ($model && $model->delete()) {
            return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
        }
        return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào để xóa.']);
    }
    /**
     * Xóa nhiều bản ghi.
     */
    public function actionDeleteSelectedData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pageId = Yii::$app->request->post('pageId');
        $ids = Yii::$app->request->post('ids');

        if (empty($ids) || !$pageId) {
            return $this->asJson(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        }

        try {
            $page = $this->loadPage($pageId);
            $tableName = $page->table_name;

            $models = BaseModel::withTable($tableName)->findAll([BaseModel::HIDDEN_ID_KEY => $ids]);

            if (!$models) {
                return $this->asJson(['success' => false, 'message' => 'Không tìm thấy bản ghi nào để xóa.']);
            }

            foreach ($models as $model) {
                $model->delete();
            }

            return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
        } catch (\Throwable $e) {
            return $this->asJson(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
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

            // Loại bỏ cột 'id' khỏi danh sách cột dự kiến
            $expectedColumns = array_filter($expectedColumns, fn($column) => strtolower($column) !== BaseModel::HIDDEN_ID_KEY);

            // Lấy header từ tệp Excel và loại bỏ 'id' nếu tồn tại
            $excelHeaders = $this->getColumnHeadersFromExcel($filePath);
            $excelHeaders = array_filter($excelHeaders, fn($header) => strtolower($header) !== BaseModel::HIDDEN_ID_KEY);

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
                    return strtolower($key) !== BaseModel::HIDDEN_ID_KEY;
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
    public function actionExportExcel($pageId)
    {
        $page = Page::findOne(['id' => $pageId]);
        if (!$page) {
            throw new NotFoundHttpException('Trang không tồn tại.');
        }

        $tableName = $page->table_name;

        $columns = (new Query())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where(['table_name' => $tableName])
            ->andWhere(['<>', 'column_name', BaseModel::HIDDEN_ID_KEY])
            ->all();

        $columnNames = array_map(fn($column) => $column['column_name'], $columns);

        $query = BaseModel::withTable($tableName)->find();

        if ($search = Yii::$app->request->get('search')) {
            $condition = [];

            foreach ($columnNames as $columnName) {
                if ($columnName === BaseModel::HIDDEN_ID_KEY) {
                    continue;
                }
                $columnNameQuoted = "\"$columnName\"";
                $condition[] = "LOWER(unaccent(CAST($columnNameQuoted AS TEXT))) ILIKE LOWER(unaccent(:searchTerm))";
            }

            if (!empty($condition)) {
                $query->where(implode(' OR ', $condition), [':searchTerm' => '%' . $search . '%']);
            }
        }

        // if ($sort = Yii::$app->request->get('sort')) {
        //     $direction = 'ASC';
        //     if (strpos($sort, '-') === 0) {
        //         $direction = 'DESC';
        //         $sort = substr($sort, 1); 
        //     }
        //     $query->orderBy([$sort => $direction]);
        // }

        $data = $query->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $columnIndex++;
        }

        $rowIndex = 2;
        foreach ($data as $row) {
            $columnIndex = 1;
            foreach ($columnNames as $column) {
                if (isset($row->$column)) {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $row->$column);
                }
                $columnIndex++;
            }
            $rowIndex++;
        }

        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $tableName . '.xlsx';
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        Yii::$app->response->sendFile($tempFilePath, $fileName)->on(Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $tempFilePath);
    }

    // Xuất Template
    public function actionExportExcelHeader($pageId)
    {

        $page = Page::findOne(['id' => $pageId]);
        if (!$page) {
            // throw
        }


        $tableName = $page->table_name;
        $columnNames = Yii::$app->db->getSchema()->getTableSchema($tableName)->getColumnNames();

        if (empty($columnNames)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột nào trong bảng.']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnIndex = 1;
        foreach ($columnNames as $column) {
            if ($column == BaseModel::HIDDEN_ID_KEY) {
                continue;
            }
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
        $sheet->getStyle('A1:' . chr(63 + $columnIndex) . '1')->applyFromArray($headerStyle);

        // Tạo file Excel và lưu
        $uploadDir = Yii::getAlias('@webroot/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $tableName . '-template.xlsx';
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        Yii::$app->response->sendFile($tempFilePath, $fileName)->on(Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $tempFilePath);
    }
}