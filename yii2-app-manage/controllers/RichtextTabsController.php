<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\RichtextTab;
class RichtextTabsController extends Controller
{
    public function actionIndex()
    {
        $richtextTabs = RichtextTab::find()->all();

        return $this->render('index', [
            'richtextTabs' => $richtextTabs,
        ]);
    }
    public function actionCreateRichtextTab($tabId)
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content');

            $tab = new Tab();
            $tab->user_id = Yii::$app->user->id;
            $tab->tab_type = 'richtext';
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            $richtextTab = new RichtextTab();
            $richtextTab->tab_id = $tabId;
            $richtextTab->content = $content;
            $richtextTab->created_at = date('Y-m-d H:i:s');
            $richtextTab->updated_at = date('Y-m-d H:i:s');

            if ($richtextTab->save()) {
                Yii::$app->session->setFlash('success', 'Thêm nội dung thành công!');
                return $this->redirect(['table-tabs']);
            } else {
                Yii::$app->session->setFlash('error', 'Không thể lưu nội dung.');
                return $this->redirect(['table-tabs']);
            }
        }

        return $this->render('create', [
            'tabId' => $tabId,
        ]);
    }
    public function actionEditRichtextTab($id)
    {
        $richtextTab = RichtextTab::findOne($id);
        if (!$richtextTab) {
            return $this->asJson(['success' => false, 'message' => 'Nội dung không tồn tại.']);
        }

        if (Yii::$app->request->isPost) {
            $richtextTab->content = Yii::$app->request->post('content');
            $richtextTab->updated_at = date('Y-m-d H:i:s');

            if ($richtextTab->save()) {
                return $this->asJson(['success' => true, 'message' => 'Chỉnh sửa nội dung thành công!']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Không thể lưu thay đổi.']);
            }
        }

        return $this->render('edit', [
            'richtextTab' => $richtextTab,
        ]);
    }
    public function actionDeleteRichtextTab($id)
    {
        $richtextTab = RichtextTab::findOne($id);
        if ($richtextTab) {
            $richtextTab->delete();
            return $this->asJson(['success' => true, 'message' => 'Xóa nội dung thành công!']);
        }

        return $this->asJson(['success' => false, 'message' => 'Nội dung không tồn tại.']);
    }


}