<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Change Password Form
 */
class ChangePasswordForm extends Model
{
    public $old_password;
    public $new_password;
    public $confirm_new_password;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['old_password', 'required', 'message' => 'Mật khẩu cũ không được để trống.'],
            ['old_password', 'validateOldPassword'],

            ['new_password', 'required', 'message' => 'Mật khẩu mới không được để trống.'],
            ['new_password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 'message' => 'Mật khẩu mới quá ngắn.'],
            ['confirm_new_password', 'required', 'message' => 'Xác nhận mật khẩu mới không được để trống.'],
            ['confirm_new_password', 'compare', 'compareAttribute' => 'new_password', 'message' => 'Mật khẩu mới không khớp.'],
        ];
    }

    /**
     * Validates the old password.
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, 'Mật khẩu cũ không đúng.');
            }
        }
    }

    /**
     * Change password
     *
     * @return bool if password was changed
     */
    public function changePassword()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->setPassword($this->new_password);
            return $user->save();
        }
        return false;
    }

    /**
     * Finds the user by [[id]]
     *
     * @return User|null
     */
    private function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findIdentity(Yii::$app->user->id);
        }
        return $this->_user;
    }
}