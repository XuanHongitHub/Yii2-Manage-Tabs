<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Tab;
use app\models\TableTab;
use app\models\Menu;
use yii\web\NotFoundHttpException;
use yii\web\Exception;
use yii\filters\AccessControl;


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
        $tabMenus = Menu::find()
            // ->where(['parent_id' => NULL])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();
        return $this->render('index', [
            'tabMenus' => $tabMenus,

        ]);
    }
    public function actionCreate()
    {
        $tabs = Tab::find()->where(['menu_id' => null])->all();

        $menus = Menu::find()
            ->leftJoin('menu AS parent_menu', 'parent_menu.id = menu.parent_id') // Đặt alias cho bảng menu
            ->where(['menu.parent_id' => null]) // Lọc các bản ghi không có parent_id (null)
            ->all();

        return $this->render('create', [
            'tabs' => $tabs,
            'menus' => $menus,
        ]);
    }

    public function actionCreateOrUpdateMenu()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
    
            $model = Menu::findOne($data['id']) ?? new Menu();
            $transaction = Yii::$app->db->beginTransaction();
    
            try {
                $model->id = $data['id'] ?? null;
                $model->name = $data['name'];
                $model->menu_type = $data['menu_type'];
                $model->icon = $data['icon'];
                $model->status = $data['status'];
                $model->position = $data['position'];
    
                if (!$model->save()) {
                    throw new \Exception('Không thể lưu menu.', 1);
                }
    
                // Liên kết Tabs với Menu
                $selectedTabs = $data['selectedTabs'] ?? [];
                if (!empty($selectedTabs)) {
                    Tab::updateAll(['menu_id' => null], ['menu_id' => $model->id]); // Xóa liên kết cũ
                    Tab::updateAll(['menu_id' => $model->id], ['id' => $selectedTabs]); // Thêm liên kết mới
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
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Lấy menu con đã liên kết
        $childTabs = Tab::find()->where(['menu_id' => $menu_id])->all();
        $childMenus = Menu::find()->where(['parent_id' => $menu_id])->all();

        // Lấy các tab/menu tiềm năng (chưa liên kết)
        $potentialTabs = Tab::find()->where(['menu_id' => null])->all();
        $potentialMenus = Menu::find()
        ->leftJoin('menu AS parent_menu', 'parent_menu.id = menu.parent_id')  // Đặt alias cho bảng menu
        ->where(['menu.parent_id' => null])  // Lọc các bản ghi không có parent_id (null)
        // ->andWhere(['!=', 'id', $menu_id])
        ->all();


        return [
            'success' => true,
            'childTabs' => array_map(fn($tab) => ['id' => $tab->id, 'tab_name' => $tab->tab_name], $childTabs),
            'childMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $childMenus),
            'potentialTabs' => array_map(fn($tab) => ['id' => $tab->id, 'tab_name' => $tab->tab_name], $potentialTabs),
            'potentialMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $potentialMenus),
        ];
    }
    public function actionSaveSubmenu()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $menuId = Yii::$app->request->post('menu_id');
        $selectedTabs = Yii::$app->request->post('selectedTabs', []);
        $selectedMenus = Yii::$app->request->post('selectedMenus', []);

        // Xóa liên kết cũ
        Tab::updateAll(['menu_id' => null], ['menu_id' => $menuId]);
        Menu::updateAll(['parent_id' => null], ['parent_id' => $menuId]);

        // Thêm liên kết mới
        Tab::updateAll(['menu_id' => $menuId], ['id' => $selectedTabs]);
        Menu::updateAll(['parent_id' => $menuId], ['id' => $selectedMenus]);

        return ['success' => true];
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

        foreach ($menus as $tab) {
            $tabMenu = Menu::findOne($tab['id']);
            if ($tabMenu) {
                $tabMenu->position = $tab['position'];
                $tabMenu->save(false);
            }
        }

        return ['success' => true, 'message' => 'Sắp xếp thành công.'];
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

        // Đặt menu_id thành NULL trong bảng tab trước khi xóa
        Tab::updateAll(['menu_id' => null], ['menu_id' => $menuId]);

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