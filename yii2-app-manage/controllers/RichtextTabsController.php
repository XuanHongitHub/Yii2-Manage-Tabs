<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;

class RichtextTabsController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $richtext_tab = Tab::find()->where(['tab_type' => 'richtext'])
            ->where(['user_id' => $userId])
            ->all();

        return $this->render('index', [
            'richtext_tab' => $richtext_tab,
        ]);
    }
    public function actionCreateRichtextTabs()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tab_name = Yii::$app->request->post('tab_name');
            $content = Yii::$app->request->post('content');

            $tab = new Tab();
            $tab->user_id = $userId;
            $tab->tab_type = 'richtext';
            $tab->tab_name = $tab_name;
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            // if (!empty($content)) {
            //     $tab->content = $content; 
            // }

            if ($tab->save()) {
                $filePath = Yii::getAlias('@runtime/richtext/' . $tab->id . '.txt');
                try {
                    file_put_contents($filePath, $content);
                    Yii::$app->session->setFlash('info', 'Created successfully!');
                } catch (\Exception $e) {
                    Yii::error('Không thể tạo file: ' . $e->getMessage());
                    Yii::$app->session->setFlash('error', 'An error occurred while saving the file.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'An error occurred while creating the tab. Please try again.');
            }

            return $this->redirect(['tabs/settings', 'activeTab' => 'addRichtextTab']);

        }

        return $this->redirect(['tabs/settings', 'activeTab' => 'addRichtextTab']);
    }

    public function actionDetail($id)
    {
        $richtextTab = Tab::find()->where(['id' => $id])->one();
        $filePath = Yii::getAlias('@runtime/richtext/' . $id . '.txt');
        $content = file_exists($filePath) ? file_get_contents($filePath) : '';

        return $this->render('_detail', [
            'richtextTab' => $richtextTab,
            'content' => $content,
        ]);
    }

}