<?
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $user = $auth->createRole('user');
        $auth->add($user);

        $login = $auth->createPermission('login');
        $auth->add($login);

        $auth->addChild($admin, $login);
        $auth->addChild($user, $login);

        echo "RBAC initialized.\n";
    }
}