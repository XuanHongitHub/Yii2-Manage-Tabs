<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Page;

use app\models\Menu;
use yii\web\NotFoundHttpException;
use yii\web\Exception;
use yii\filters\AccessControl;
use yii\db\Query;


class MenusController extends Controller
{
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
    public function actionIndex()
    {
        $menus = Menu::find()
            // ->where(['parent_id' => NULL])
            ->orderBy([
                'position' => SORT_ASC,
            ])
            ->all();
        $pages = Page::find()
            ->all();
        return $this->render('index', [
            'menus' => $menus,
            'pages' => $pages,
        ]);
    }
    public function actionCreate()
    {
        $menus = Menu::find()
            ->leftJoin('manager_menu AS parent_menu', 'parent_menu.id = manager_menu.parent_id')
            ->where(['manager_menu.parent_id' => null])
            ->andWhere(['manager_menu.status' => 0])
            ->andWhere(['manager_menu.deleted' => 0])
            ->andWhere([
                'not in',
                'manager_menu.id',
                (new Query())
                    ->select('menu_id')
                    ->from('manager_page') // Giả sử bảng lưu các trang là `manager_page`
                    ->where('manager_page.menu_id = manager_menu.id') // Lọc ra các menu đã có manager_page liên kết
            ])
            ->all();

        return $this->render('create', [
            'menus' => $menus,
        ]);
    }

    public function actionStore()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model = new Menu(); // Tạo mới một đối tượng Menu

                // Gán các giá trị vào model
                $model->name = $data['name']; // Gán tên menu
                $model->parent_id = $data['parentId'] ?? null; // Gán parent_id, cho phép null
                $model->icon = $data['icon'] ?? null; // Gán icon, cho phép null

                // Lưu vào cơ sở dữ liệu
                if (!$model->save()) {
                    throw new \Exception('Không thể lưu menu.', 1);
                }

                // Commit transaction nếu thành công
                $transaction->commit();

                return $this->asJson(['success' => true, 'message' => 'Thành công.']);
            } catch (\Exception $e) {
                // Rollback nếu có lỗi
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }


    public function actionUpdateMenu()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $model = Menu::findOne($data['id']) ?? new Menu();
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model->id = $data['id'] ?? null;
                $model->name = $data['name'];
                $model->icon = $data['icon'];
                $model->status = $data['status'];
                $model->position = $data['position'];

                if (!$model->save()) {
                    throw new \Exception('Không thể lưu menu.', 1);
                }

                // Liên kết Pages với Menu
                $selectedPages = $data['selectedPages'] ?? [];
                if (!empty($selectedPages)) {
                    Page::updateAll(['menu_id' => null], ['menu_id' => $model->id]); // Xóa liên kết cũ
                    Page::updateAll(['menu_id' => $model->id], ['id' => $selectedPages]); // Thêm liên kết mới
                }

                // Liên kết Menu con với Menu cha
                $selectedMenus = $data['selectedMenus'] ?? [];
                if (!empty($selectedMenus)) {
                    Menu::updateAll(['parent_id' => null], ['parent_id' => $model->id]); // Xóa liên kết cũ
                    Menu::updateAll(['parent_id' => $model->id], ['id' => $selectedMenus]); // Thêm liên kết mới
                }

                $transaction->commit();
                return $this->asJson(['success' => true, 'message' => 'Thành công.']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }

    public function actionGetSubmenu($menu_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $menu = Menu::findOne($menu_id);
        if (!$menu) {
            return [
                'success' => false,
                'message' => 'Menu không tồn tại.'
            ];
        }

        $childPages = Page::find()
            ->where(['menu_id' => $menu_id])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();

        $childMenus = Menu::find()
            ->where(['parent_id' => $menu_id])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();

        // Lấy các page/menu tiềm năng (chưa liên kết)
        $potentialPages = Page::find()
            ->where(['menu_id' => null])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();

        $potentialMenus = Menu::find()
            ->leftJoin('manager_menu AS parent_menu', 'parent_menu.id = manager_menu.parent_id') // Đặt alias cho bảng menu
            ->where(['manager_menu.parent_id' => null])
            ->andWhere([
                'not in',
                'manager_menu.id',
                (new Query())
                    ->select('parent_id')
                    ->from('manager_menu')
                    ->where(['not', ['parent_id' => null]])
            ])
            ->andWhere(['manager_menu.status' => 0])
            ->andWhere(['manager_menu.deleted' => 0])
            ->all();


        return [
            'success' => true,
            'isChildMenu' => $menu->parent_id !== null,
            'childPages' => array_map(fn($page) => ['id' => $page->id, 'name' => $page->name], $childPages),
            'childMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $childMenus),
            'potentialPages' => array_map(fn($page) => ['id' => $page->id, 'name' => $page->name], $potentialPages),
            'potentialMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $potentialMenus),
        ];
    }
    public function actionSaveSubMenu()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            if (empty($data['menuId'])) {
                return $this->asJson(['success' => false, 'message' => 'menuId menu không được để trống.']);
            }

            $model = Menu::findOne($data['menuId']);
            if (!$model) {
                return $this->asJson(['success' => false, 'message' => 'Menu không tồn tại.']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {

                if (!$model->save()) {
                    throw new \Exception('Không thể lưu menu.');
                }

                $selectedPages = $data['selectedPages'] ?? [];
                if (!empty($selectedPages)) {
                    Page::updateAll(['menu_id' => null], ['menu_id' => $model->id]); // Xóa liên kết cũ
                    Page::updateAll(['menu_id' => $model->id], ['id' => $selectedPages]); // Thêm liên kết mới
                }

                $selectedMenus = $data['selectedMenus'] ?? [];
                if (!empty($selectedMenus)) {
                    Menu::updateAll(['parent_id' => null], ['parent_id' => $model->id]); // Xóa liên kết cũ
                    Menu::updateAll(['parent_id' => $model->id], ['id' => $selectedMenus]); // Thêm liên kết mới
                }

                $transaction->commit();
                return $this->asJson(['success' => true, 'message' => 'Cập nhật thành công.']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }
    public function actionSaveSubPage()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            if (empty($data['menuId'])) {
                return $this->asJson(['success' => false, 'message' => 'menuId không được để trống.']);
            }

            $menuModel = Menu::findOne($data['menuId']);
            if (!$menuModel) {
                return $this->asJson(['success' => false, 'message' => 'Menu không tồn tại.']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$menuModel->save()) {
                    throw new \Exception('Không thể lưu menu.');
                }

                $selectedPages = $data['selectedPages'] ?? [];
                $sortedData = $data['sortedData'] ?? [];
                Yii::error("selectedPages: " . print_r($selectedPages, true));

                // Cập nhật các page con
                if (!empty($selectedPages)) {
                    // Xóa liên kết cũ
                    Page::updateAll(['menu_id' => null], ['menu_id' => $data['menuId']]);
                    Page::updateAll(
                        ['menu_id' => $menuModel->id],
                        ['id' => $selectedPages]
                    );

                    foreach ($sortedData as $page) {
                        $pageModel = Page::findOne($page['id']);
                        if ($pageModel) {
                            $pageModel->position = $page['position'];
                            if (!$pageModel->save(false)) {
                                throw new \Exception("Không thể lưu subpage ID: {$page['id']}.");
                            }
                        }
                    }
                }

                $transaction->commit();
                return $this->asJson(['success' => true, 'message' => 'Cập nhật thành công.']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }

    // Status
    public function actionUpdateHideStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $hideStatus = Yii::$app->request->post('hideStatus', []);
        if (empty($hideStatus)) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
        }

        foreach ($hideStatus as $id => $status) {
            $tabMenu = Menu::findOne($id);
            if ($tabMenu) {
                $tabMenu->status = $status;
                $tabMenu->save(false);
            }
        }

        return ['success' => true, 'message' => 'Cập nhật trạng thái thành công.'];
    }
    // Sort
    public function actionUpdateSortOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $menus = Yii::$app->request->post('menus', []);
        if (empty($menus)) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
        }

        foreach ($menus as $page) {
            $tabMenu = Menu::findOne($page['id']);
            if ($tabMenu) {
                $tabMenu->position = $page['position'];
                $tabMenu->save(false);
            }
        }

        return ['success' => true, 'message' => 'Sắp xếp thành công.'];
    }

    public function actionSaveSort()
    {
        // Kiểm tra nếu có dữ liệu gửi đến
        if (Yii::$app->request->isAjax) {
            $sortedIDs = Yii::$app->request->post('sortedIDs'); // Lấy mảng các ID được sắp xếp

            // Lặp qua từng ID và cập nhật trường position
            foreach ($sortedIDs as $index => $id) {
                $menu = Menu::findOne($id); // Tìm bản ghi theo ID
                if ($menu) {
                    $menu->position = $index + 1; // Cập nhật vị trí (thứ tự trong mảng)
                    $menu->save(); // Lưu thay đổi
                }
            }

            // Trả về kết quả JSON
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Sắp xếp thành công.'];
        }

        // Nếu không phải AJAX request, trả về lỗi
        return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
    }
    // Sort Delete
    public function actionDeleteMenu()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $menuId = Yii::$app->request->post('menuId');
        $tabMenu = Menu::findOne($menuId);

        if (!$tabMenu) {
            return ['success' => false, 'message' => 'Không tìm thấy menu.'];
        }

        $tabMenu->deleted = 1; // Xóa mềm
        if ($tabMenu->save(false)) {
            return ['success' => true, 'message' => 'Xóa mềm thành công.'];
        }

        return ['success' => false, 'message' => 'Xóa mềm thất bại.'];
    }

    public function actionDeletePermanentlyMenu()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $menuId = Yii::$app->request->post('menuId');
        $tabMenu = Menu::findOne($menuId);

        if (!$tabMenu) {
            Yii::$app->session->setFlash('error', 'Không tìm thấy menu.');
            return $this->asJson(['success' => false, 'message' => 'Không tìm thấy menu.']);
        }

        // Đặt menu_id thành NULL trong bảng page trước khi xóa
        Page::updateAll(['menu_id' => null], ['menu_id' => $menuId]);

        if ($tabMenu->delete()) {
            Yii::$app->session->setFlash('success', 'Xóa hoàn toàn thành công.');
            return $this->asJson(['success' => true, 'message' => 'Xóa hoàn toàn thành công.']);
        } else {
            Yii::$app->session->setFlash('error', 'Xóa thất bại.');
            return $this->asJson(['success' => false, 'message' => 'Xóa thất bại.']);
        }
    }

    // Restore
    public function actionRestoreMenu()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['menuId'])) {
            $menuId = $postData['menuId'];

            $affectedRows = Menu::updateAll(
                ['deleted' => 0],
                ['id' => $menuId]
            );

            if ($affectedRows > 0) {
                Yii::$app->session->setFlash('success', 'Khôi phục thành công.');
                return $this->asJson(['success' => true, 'message' => 'Khôi phục thành công.']);
            } else {
                Yii::$app->session->setFlash('error', 'Không có bản ghi nào được cập nhật.');
                return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào được cập nhật.']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Thiếu menuId.');
            return $this->asJson(['success' => false, 'message' => 'Thiếu menuId.']);
        }
    }
}