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
    public function actionGetChild($id)
    {
        $tabMenu = Menu::findOne($id);

        if ($tabMenu === null) {
            Yii::$app->response->statusCode = 404;
            return 'Menu không tồn tại.';
        }

        // Chỉ lấy menu_id và tab_name của Tab đã chọn làm con
        $childTabs = Tab::find()
            ->select(['id', 'tab_name', 'menu_id'])
            ->where(['menu_id' => $tabMenu->id])
            ->asArray()
            ->all();

        // Chỉ lấy id và name của TabMenu đã chọn làm con
        $childMenus = Menu::find()
            ->select(['id', 'name'])
            ->where(['parent_id' => $tabMenu->id])
            ->asArray()
            ->all();

        // Chỉ lấy id, tab_name và menu_id của Tab có thể làm con
        $potentialChildTabs = Tab::find()
            ->select(['id', 'tab_name', 'menu_id'])
            ->where(['menu_id' => null])
            ->asArray()
            ->all();

        // Chỉ lấy id và name của TabMenu có thể làm con
        $potentialChildMenus = Menu::find()
            ->select(['id', 'name'])
            ->where(['parent_id' => null])
            ->asArray()
            ->all();

        // Trả về dữ liệu dưới dạng JSON
        return json_encode([
            'childTabs' => $childTabs,
            'childMenus' => $childMenus,
            'potentialChildTabs' => $potentialChildTabs,
            'potentialChildMenus' => $potentialChildMenus,
        ]);
    }


    public function actionMenuCreate()
    {

        return $this->render('create', []);
    }
    public function actionMenuList()
    {
        $tabMenus = Menu::find()
            ->where(['parent_id' => NULL])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();
        return $this->render('index', [
            'tabMenus' => $tabMenus,

        ]);
    }
    public function actionCreateOrUpdateMenu()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $model = Menu::findOne($data['id']) ?? new Menu();

            $model->id = $data['id'];
            $model->name = $data['name'];
            $model->menu_type = $data['menu_type'];
            $model->icon = $data['icon'];
            $model->status = $data['status'];
            $model->position = $data['position'];

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Thành công.');
                return $this->asJson(['success' => true, 'message' => 'Thành công.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Có lỗi xảy ra.', 'errors' => $model->errors]);
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