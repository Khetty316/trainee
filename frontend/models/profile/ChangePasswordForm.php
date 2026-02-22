<?php

namespace frontend\models\profile;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use common\models\User;

/**
 * Password reset form
 */
class ChangePasswordForm extends Model {

    public $password;
    public $oldPassword;
    public $passwordRepeat;

    /**
     * @var \common\models\User
     */

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['oldPassword', 'password', 'passwordRepeat'], 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'message' => 'Repeated password does not match'],
            ['oldPassword', 'validateOldPassword']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'oldPassword' => 'Current Password',
            'password' => 'New Password',
            'passwordRepeat' => 'Repeat New Password',
        ];
    }

    public function validateOldPassword() {

        if (!Yii::$app->security->validatePassword($this->oldPassword, Yii::$app->user->identity->password)) {
            $this->addError('oldPassword', 'Incorrect old password.');
        }
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword() {
        if ($this->validate()) {
            $user = User::findOne(Yii::$app->user->identity->id);
            $user->setPassword($this->password);
            return ($user->update(false));
        }else{
            return false;
        }
    }

}
