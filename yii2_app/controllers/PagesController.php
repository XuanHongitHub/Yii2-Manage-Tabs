<?php

namespace app\controllers;

use app\models\BaseModel;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Page;
use app\models\Menu;
use Exception;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use yii\data\ActiveDataProvider;
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
            ->innerJoin('manager_menu_page', 'manager_page.id = manager_menu_page.page_id')
            ->where(['status' => 0, 'menu_id' => $menuId, 'deleted' => 0])
            ->orderBy(['manager_menu_page.id' => SORT_ASC])
            ->all();

        if (!$pages) {
            throw new NotFoundHttpException('Không có Page nào. Vui lòng thêm Page!');
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
                $configColumns = Config::find()
                    ->select(['column_name', 'is_visible', 'display_name', 'column_width', 'column_position', 'menu_id'])
                    ->where(['page_id' => $page->id])
                    ->andFilterWhere(['=', 'menu_id', $menuId])
                    ->indexBy('column_name')
                    ->asArray()
                    ->all();

                $nullMenuConfigs = Config::find()
                    ->select(['column_name', 'display_name'])
                    ->where(['page_id' => $page->id])
                    ->andFilterWhere(['menu_id' => null])
                    ->indexBy('column_name')
                    ->asArray()
                    ->all();

                foreach ($nullMenuConfigs as $config) {
                    if (isset($configColumns[$config['column_name']])) {
                        if ($configColumns[$config['column_name']]['display_name'] === null) {
                            $configColumns[$config['column_name']]['display_name'] = $config['display_name'];
                        }
                    } else {
                        $configColumns[$config['column_name']] = [
                            'column_name' => $config['column_name'],
                            'display_name' => $config['display_name'],
                            'is_visible' => null,
                            'column_width' => null,
                            'column_position' => null,
                        ];
                    }
                }
                usort($configColumns, function ($a, $b) {
                    return $a['column_position'] <=> $b['column_position'];
                });
                $configColumns = array_values($configColumns);
                return $this->render('singlePageTable', [
                    'dataProvider' => $dataProvider,
                    'columns' => $columnNames,
                    'menu' => $menu,
                    'pageId' => $page->id,
                    'configColumns' => $configColumns,
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
        $menuId = Yii::$app->request->get('menuId');
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

            // Fetch all configurations in a single query
            $configs = Config::find()
                ->where(['page_id' => $page->id])
                ->andFilterWhere(['=', 'menu_id', $menuId])
                ->orWhere(['menu_id' => null])
                ->asArray()
                ->all();

            $configColumns = [];

            foreach ($configs as $config) {
                if (isset($configColumns[$config['column_name']])) {
                    $configColumns[$config['column_name']]['display_name'] ??= $config['display_name'];
                } else {
                    $configColumns[$config['column_name']] = $config;
                }
            }

            usort($configColumns, fn($a, $b) => $a['column_position'] <=> $b['column_position']);

            $configColumns = array_values($configColumns);


            return $this->renderAjax('_tableData', [
                'dataProvider' => $dataProvider,
                'columns' => $columnNames,
                'pageId' => $pageId,
                'menuId' => $menuId,
                'configColumns' => $configColumns,
            ]);
        } elseif ($page->type === 'richtext') {
            $content = $page->content;

            return $this->renderAjax('_richtextData', [
                'page' => $page,
                'content' => $content,
                'pageId' => $page->id,
                'menuId' => $menuId,
            ]);
        }

        return 'No data';
    }
    /**
     * Update Config Table Page
     * 
     */
    public function actionSaveColumnsConfig()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $columnsConfig = Yii::$app->request->post('columns_config');
        $menuId = Yii::$app->request->post('menuId');
        $pageId = Yii::$app->request->post('pageId');

        if (!$columnsConfig || (!$menuId && !$pageId)) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
        }

        foreach ($columnsConfig as $column) {
            $columnName = $column['column_name'];
            $isVisible = in_array($column['is_visible'], ['true', '1'], true) ? 1 : 0;
            $columnPosition = (int) $column['column_position'];

            if ($menuId === null) {
                $config = new Config();
                $config->column_name = $columnName;
                $config->menu_id = null;
                $config->page_id = null;
                $config->is_visible = $isVisible;
                $config->column_position = $columnPosition;
            } else {
                $config = Config::findOne([
                    'column_name' => $columnName,
                    'menu_id' => $menuId,
                    'page_id' => $pageId
                ]) ?? new Config();

                $config->column_name = $columnName;
                $config->menu_id = $menuId;
                $config->page_id = $pageId;
                $config->is_visible = $isVisible;
                $config->column_position = $columnPosition;
            }

            if (!$config->save()) {
                return [
                    'success' => false,
                    'message' => 'Không thể lưu tùy chỉnh.',
                    'errors' => $config->getErrors()
                ];
            }
        }

        return ['success' => true, 'message' => 'Cập nhật tùy chỉnh cột thành công.'];
    }
    /**
     * Update Column Width
     * 
     */
    public function actionSaveColumnsWidth()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $columnsConfig = Yii::$app->request->post('columns_config');
        $menuId = Yii::$app->request->post('menuId');
        $pageId = Yii::$app->request->post('pageId');

        if (!$columnsConfig || (!$menuId && !$pageId)) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
        }

        foreach ($columnsConfig as $columnData) {
            $columnName = $columnData['column_name'] ?? null;
            $columnWidth = isset($columnData['column_width']) ? (int)$columnData['column_width'] : null;
            $columnPosition = (int) $columnData['column_position'];

            if (!$columnName || $columnWidth === null) {
                return [
                    'success' => false,
                    'message' => 'Dữ liệu không đầy đủ cho cột.'
                ];
            }

            $config = Config::findOne([
                'column_name' => $columnName,
                'menu_id' => $menuId,
                'page_id' => $pageId
            ]) ?? new Config();

            $config->column_name = $columnName;
            $config->menu_id = $menuId;
            $config->page_id = $pageId;
            $config->column_width = $columnWidth;

            // Kiểm tra nếu column_position đã có giá trị thì không cập nhật
            if ($config->column_position === null) {
                $config->column_position = $columnPosition;
            }

            if (!$config->save()) {
                return [
                    'success' => false,
                    'message' => 'Không thể lưu thông tin cột.',
                    'errors' => $config->getErrors()
                ];
            }
        }

        return ['success' => true, 'message' => 'Cập nhật độ rộng cột thành công.'];
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

        if ($model->save()) {
            return $this->asJson(['success' => true, 'message' => 'Thêm dữ liệu thành công.']);
        } else {
            return $this->asJson([
                'success' => false,
                'message' => "Lỗi khi thêm dữ liệu: \n",
                'errors' => $model->errors
            ]);
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
        $file = $_FILES['import-excel-file'] ?? null;
        $pageId = Yii::$app->request->post('pageId');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK || !$pageId) {
            return $this->asJson([
                'success' => false,
                'message' => 'Thiếu dữ liệu cần thiết hoặc lỗi tệp.',
            ]);
        }

        $page = Page::findOne($pageId);
        if (!$page) {
            return $this->asJson([
                'success' => false,
                'message' => 'Không tìm thấy pageId.',
            ]);
        }

        $tableName = $page->table_name;

        try {
            $filePath = $file['tmp_name'];
            $data = iterator_to_array($this->parseExcel($filePath));

            if (empty($data)) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Tập tin không chứa dữ liệu.',
                ]);
            }

            $model = BaseModel::withTable($tableName);
            $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);

            if (!$tableSchema) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Không tìm thấy bảng trong cơ sở dữ liệu.',
                ]);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                Yii::$app->db->createCommand()->delete($tableName)->execute();

                $headers = array_shift($data);

                if (empty($headers)) {
                    $transaction->rollBack();
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Không tìm thấy tiêu đề cột trong file Excel.',
                    ]);
                }

                $invalidColumns = array_diff($headers, array_keys($tableSchema->columns), [BaseModel::HIDDEN_ID_KEY]);

                if (!empty($invalidColumns)) {
                    $errorMessage = "\nCác tiêu đề cột trong tệp Excel không khớp với các cột cơ sở dữ liệu. Vui lòng xem lại các chi tiết sau:\n\n";

                    $errorMessage .= "Các cột trong tệp Excel:\n" . "<div class='d-flex gap-3'>" . implode("", array_map(function ($header) {
                        return "<div class='p-2 text-danger'>" . htmlspecialchars($header) . "</div>";
                    }, $headers)) . "</div>\n\n";

                    $errorMessage .= "Các cột dự kiến ​​trong bảng:\n" . "<div class='d-flex gap-3'>" . implode("", array_map(function ($column) {
                        return "<div class='p-2 text-success'>" . htmlspecialchars($column) . "</div>";
                    }, array_diff(array_keys($tableSchema->columns), [BaseModel::HIDDEN_ID_KEY]))) . "</div>\n\n";

                    $transaction->rollBack();
                    return $this->asJson([
                        'success' => false,
                        'message' => $errorMessage,
                    ]);
                }

                $columnMap = array_flip($headers);

                $errors = [];
                foreach ($data as $rowIndex => $row) {
                    $rowData = [];
                    foreach ($columnMap as $excelColumn => $tableColumnIndex) {
                        if (isset($row[$tableColumnIndex])) {
                            $excelColumnString = (string) $excelColumn;
                            $rowData[$excelColumnString] = (string) $row[$tableColumnIndex];
                        }
                    }

                    $modelInstance = clone $model;

                    // Sử dụng setAttribute thay vì trực tiếp gán giá trị
                    foreach ($rowData as $key => $value) {
                        $modelInstance->setAttribute($key, $value);
                    }

                    if (!$modelInstance->save()) {
                        $rowNumber = $rowIndex + 2;
                        $errors[] = "Lỗi tại dòng {$rowNumber}: " . json_encode($modelInstance->getErrors(), JSON_UNESCAPED_UNICODE);
                    }
                }

                if (!empty($errors)) {
                    $transaction->rollBack();
                    return $this->asJson([
                        'success' => false,
                        'message' => implode("\n", $errors),
                    ]);
                }

                $transaction->commit();
                return $this->asJson(['success' => true, 'message' => 'Nhập dữ liệu thành công.']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            return $this->asJson([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
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

        if ($sort = Yii::$app->request->get('sort')) {
            $direction = 'DESC';

            if (substr($sort, 0, 1) === '-') {
                $direction = 'ASC';
                $sort = ltrim($sort, '-');
            }

            $quotedSortColumn = "\"$sort\"";

            $query->orderBy(new \yii\db\Expression("$quotedSortColumn $direction"));
        }


        $data = $query->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnIndex = 1;
        foreach ($columnNames as $column) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $column);
            $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);
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

        $sheet->getStyle('A1:' . chr(64 + count($columnNames)) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        foreach (range('A', chr(64 + count($columnNames))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $uploadDir = Yii::getAlias('@runtime/cache');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = $tableName . '.xlsx';
        $tempFilePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        return Yii::$app->response->sendFile($tempFilePath, $fileName)->on(Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $tempFilePath);
    }


    // Xuất Template
    public function actionExportExcelHeader($pageId)
    {

        $page = Page::findOne(['id' => $pageId]);
        if (!$page) {
            throw new NotFoundHttpException('Trang không tồn tại.');
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
        $uploadDir = Yii::getAlias('@runtime/cache');
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