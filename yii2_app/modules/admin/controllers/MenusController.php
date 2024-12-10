<?php

namespace app\modules\admin\controllers;

use app\models\MenuPage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\models\Page;

use app\models\Menu;
use app\modules\admin\components\BaseAdminController;
use yii\filters\AccessControl;
use yii\db\Query;

class MenusController extends BaseAdminController
{

    public function actionIndex()
    {
        $menus = Menu::find()
            ->orderBy([
                'position' => SORT_ASC,
            ])
            ->all();
        return $this->render('index', [
            'menus' => $menus,
        ]);
    }
    public function actionCreate()
    {
        $potentialMenus = Menu::find()
            ->leftJoin('manager_menu AS parent_menu', 'parent_menu.id = manager_menu.parent_id')
            ->where(['manager_menu.parent_id' => null])
            ->andWhere(['manager_menu.status' => 0])
            ->andWhere(['manager_menu.deleted' => 0])
            ->andWhere([
                'not in',
                'manager_menu.id',
                (new Query())
                    ->select('menu_id')
                    ->from('manager_menu_page')
                    ->where('manager_menu_page.menu_id = manager_menu.id')
            ])
            ->all();
        $potentialPages = Page::find()
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();
        return $this->render('create', [
            'potentialMenus' => $potentialMenus,
            'potentialPages' => $potentialPages,
        ]);
    }
    /**
     * Thêm menu
     * @throws \Exception
     * @return Yii\web\Response
     */
    public function actionStore()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            if (empty($data['name'])) {
                return $this->asJson(['success' => false, 'message' => 'Tên menu không được để trống.']);
            }

            if (empty($data['icon'])) {
                return $this->asJson(['success' => false, 'message' => 'Vui lòng chọn một icon.']);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model = new Menu();
                $model->name = $data['name'];
                $model->parent_id = $data['parentId'] ?? null;
                $model->icon = $data['icon'];


                if (!$model->save()) {
                    if ($model->getErrors('name')) {
                        $errorMessages[] = "Tên menu đã tồn tại.";
                    }
                    throw new \Exception('Không thể tạo menu: ' . implode(', ', $errorMessages));
                }

                $selectedPages = $data['selectedPages'] ?? [];
                if (!empty($selectedPages)) {

                    foreach ($selectedPages as $page) {
                        $menuPage = new MenuPage();
                        $menuPage->menu_id = $model->id;
                        $menuPage->page_id = $page;

                        if (!$menuPage->save()) {
                            throw new \Exception('Không thể lưu subpage ID: ' . $page['id'] . '. Lỗi: ' . json_encode($menuPage->getErrors()));
                        }
                    }
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


    /**
     * / Update Menu
     * @throws \Exception
     * @return Yii\web\Response
     */
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

                if (!$model->save()) {
                    throw new \Exception('Không thể lưu menu.', 1);
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

        $childMenus = Menu::find()
            ->where(['parent_id' => $menu_id])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();

        $potentialMenus = Menu::find()
            ->leftJoin('manager_menu AS parent_menu', 'parent_menu.id = manager_menu.parent_id')
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
            'childMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $childMenus),
            'potentialMenus' => array_map(fn($menu) => ['id' => $menu->id, 'name' => $menu->name], $potentialMenus),
        ];
    }

    public function actionGetSubpage($menu_id)
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
            ->innerJoin('manager_menu_page', 'manager_page.id = manager_menu_page.page_id')
            ->where(['menu_id' => $menu_id])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->orderBy(['manager_menu_page.id' => SORT_ASC])
            ->all();

        $ids = ArrayHelper::getColumn($childPages, 'id');


        $potentialPages = Page::find()
            ->where(['not in', 'id', $ids])
            ->andWhere(['status' => 0])
            ->andWhere(['deleted' => 0])
            ->all();


        return [
            'success' => true,
            'isChildMenu' => $menu->parent_id !== null,
            'childPages' => array_map(fn($page) => ['id' => $page->id, 'name' => $page->name], $childPages),
            'potentialPages' => array_map(fn($page) => ['id' => $page->id, 'name' => $page->name], $potentialPages),
        ];
    }
    public function actionSaveSubMenu()
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
                $selectedMenus = $data['selectedMenus'] ?? [];
                $sortedData = $data['sortedData'] ?? [];

                // Xóa tất cả các menu con hiện tại trong menu cha
                Menu::updateAll(['parent_id' => null], ['parent_id' => $menuModel->id]);

                if ($selectedMenus) {
                    // Sắp xếp lại dữ liệu các menu con theo thứ tự đã gửi từ client
                    usort($sortedData, function ($a, $b) {
                        return $a['position'] > $b['position'];
                    });

                    // Lưu lại các menu con theo thứ tự đã sắp xếp
                    foreach ($sortedData as $menu) {
                        $menuModelToUpdate = Menu::findOne($menu['id']);
                        if ($menuModelToUpdate) {
                            $menuModelToUpdate->parent_id = $menuModel->id;
                            $menuModelToUpdate->position = $menu['position']; // Cập nhật thứ tự
                            if (!$menuModelToUpdate->save()) {
                                throw new \Exception("Không thể lưu menu con ID: {$menu['id']}." . json_encode($menuModelToUpdate->getErrors()));
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
                $selectedPages = $data['selectedPages'] ?? [];
                $sortedData = $data['sortedData'] ?? [];

                MenuPage::deleteAll(['menu_id' => $data['menuId']]);
                if ($selectedPages) {
                    usort($sortedData, function ($a, $b) {
                        return $a['position'] > $b['position'];
                    });

                    foreach ($sortedData as $page) {
                        $menuPage = new MenuPage();
                        $menuPage->menu_id = $menuModel->id;
                        $menuPage->page_id = $page['id'];
                        if (!$menuPage->save()) {
                            throw new \Exception("Không thể lưu subpage ID: {$page['id']}." . json_encode($menuPage->getErrors()));
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
        if (Yii::$app->request->isAjax) {
            $sortedIDs = Yii::$app->request->post('sortedIDs');

            foreach ($sortedIDs as $index => $id) {
                $menu = Menu::findOne($id);
                if ($menu) {
                    $menu->position = $index + 1;
                    $menu->save();
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Sắp xếp thành công.'];
        }

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

        $tabMenu->deleted = 1;
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

        Page::updateAll(['menu_id' => null], ['menu_id' => $menuId]);

        if ($tabMenu->delete()) {
            Yii::$app->session->setFlash('success', 'Xóa thành công.');
            return $this->asJson(['success' => true, 'message' => 'Xóa thành công.']);
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
