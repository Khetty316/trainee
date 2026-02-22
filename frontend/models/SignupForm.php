<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model {

    public $username;
    public $email;
    public $password;
    public $fullname;
    public $postcode;
    public $area_id;
    public $address;
//    public $contact_no;
    public $emergency_contact_person;
//    public $emergency_contact_no;
    public $staff_id;
    public $employment_type;
    public $company_name;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['username', 'trim'],
            [['username', 'fullname', 'password', 'email', 'company_name', 'employment_type', 'staff_id'], 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            [['username', 'fullname'], 'string', 'min' => 2, 'max' => 255],
            ['staff_id', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This staff id has already been taken.'],
            [['staff_id', 'company_name', 'employment_type'], 'string', 'max' => 10],
//            ['username', 'match', 'pattern' => '/^[a-z]\w*$/i','message' => 'Username only allow Alphanumeric'],
            ['username', 'match', 'pattern' => '/^\S*$/u', 'message' => 'No space allowed'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup() {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->fullname = $this->fullname;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->contact_no = "-";
        $user->staff_id = $this->staff_id;
        $user->employment_type = $this->employment_type;
        $user->company_name = $this->company_name;
        $result = $user->save(false);
        if ($user->errors) {
            \common\models\myTools\Mydebug::vardump($user->errors);
        }
        return $result; // && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user) {
        return Yii::$app->mailer
                        ->compose(
                                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                                ['user' => $user]
                        )
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Robot'])
                        ->setTo($this->email)
                        ->setSubject('Account registration at ' . Yii::$app->name)
                        ->send();
    }

}
