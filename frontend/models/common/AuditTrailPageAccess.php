<?php

namespace frontend\models\common;

use Yii;
use common\models\User;

/**
 * This is the model class for table "audit_trail_page_access".
 *
 * @property int $id
 * @property string $page
 * @property int|null $user_id
 * @property string $created_at
 *
 * @property User $user
 */
class AuditTrailPageAccess extends \yii\db\ActiveRecord {

    const staffList = [9, 7, 6, 16, 31]; // florence, lily, jennifer, alvin ang, elvin wong

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'audit_trail_page_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['page'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['page'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'page' => 'Page',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function checkaccess() {
        if (!Yii::$app->user->id) {
            return false;
        }
        
        if (!$this->avaibilityChecked()) {
            // If the staff is on the checking list
            if (in_array(Yii::$app->user->id, self::staffList)) {
                $leaveMaster = \frontend\models\office\leave\LeaveMaster::find()
                                ->where('leave_type=4 AND DATE(NOW()) BETWEEN start_date AND end_date AND leave_status NOT IN (6,7) AND requestor_id=' . Yii::$app->user->id)->one();
                
                // If the staff is WFH
                if ($leaveMaster) {
                    Yii::$app->session->set("CheckPageAccess", true);
                } else {

                    Yii::$app->session->set("CheckPageAccess", false);
                }
            }

            Yii::$app->session->set("checkDate", date('Y-m-d'));
        }
        if (Yii::$app->session->get("CheckPageAccess")) {
            if (!$this->recordPageAccess()) {
                \common\models\myTools\Mydebug::dumpFileA($this->errors);
            }
        } else {
        }
    }

    // Look in the session if already have record.
    private function avaibilityChecked() {
        $checkDate = Yii::$app->session->get("checkDate");
        if ($checkDate && $checkDate == date('Y-m-d')) {
            return true;
        } else {
            return false;
        }
    }

    private function recordPageAccess() {
        $this->user_id = Yii::$app->user->id;
        $this->page = Yii::$app->request->url;
        $this->created_at = new \yii\db\Expression('NOW()');
        return $this->save();
    }

    // Check if availability checked. 
    //      if yes, then skip to last step
    //      if No, then go step 2
    // Check if is WFH
    // Check if is on the checking list (by user id)
    // Record if need to record
}
