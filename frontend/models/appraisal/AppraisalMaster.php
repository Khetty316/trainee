<?php

namespace frontend\models\appraisal;

use Yii;
use common\models\User;
use frontend\models\appraisal\AppraisalMain;
use frontend\models\common\RefAppraisalStatus;
use frontend\models\appraisal\AppraisalMasterForm;

/**
 * This is the model class for table "appraisal_master".
 *
 * @property int $id
 * @property int|null $main_id
 * @property int|null $user_id
 * @property int|null $overall_rating
 * @property int|null $overall_review
 * @property int|null $appraisal_sts
 * @property int|null $appraise_by
 * @property string|null $appraise_date
 * @property int|null $review_by
 * @property string|null $review_date
 * @property string|null $staff_remark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 * @property User $appraiseBy
 * @property User $reviewBy
 * @property RefAppraisalStatus $appraisalSts
 * @property AppraisalMain $main
 * @property AppraisalMasterForm[] $appraisalMasterForms
 */
class AppraisalMaster extends \yii\db\ActiveRecord {

    const TYPE_RATING = 'rating';
    const TYPE_REVIEW = 'review';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'appraisal_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['main_id', 'user_id', 'overall_rating', 'overall_review', 'appraisal_sts', 'appraise_by', 'review_by', 'created_by', 'updated_by'], 'integer'],
            [['appraise_date', 'review_date', 'created_at', 'updated_at'], 'safe'],
            [['staff_remark'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['appraise_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['appraise_by' => 'id']],
            [['review_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['review_by' => 'id']],
            [['appraisal_sts'], 'exist', 'skipOnError' => true, 'targetClass' => RefAppraisalStatus::className(), 'targetAttribute' => ['appraisal_sts' => 'id']],
            [['main_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppraisalMain::className(), 'targetAttribute' => ['main_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'main_id' => 'Main ID',
            'user_id' => 'User ID',
            'overall_rating' => 'Overall Rating',
            'overall_review' => 'Overall Review',
            'appraisal_sts' => 'Appraisal Sts',
            'appraise_by' => 'Appraise By',
            'appraise_date' => 'Appraise Date',
            'review_by' => 'Review By',
            'review_date' => 'Review Date',
            'staff_remark' => 'Staff Remark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
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

    /**
     * Gets query for [[AppraiseBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraiseBy() {
        return $this->hasOne(User::className(), ['id' => 'appraise_by']);
    }

    /**
     * Gets query for [[ReviewBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewBy() {
        return $this->hasOne(User::className(), ['id' => 'review_by']);
    }

    /**
     * Gets query for [[AppraisalSts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalSts() {
        return $this->hasOne(RefAppraisalStatus::className(), ['id' => 'appraisal_sts']);
    }

    /**
     * Gets query for [[Main]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMain() {
        return $this->hasOne(AppraisalMain::className(), ['id' => 'main_id']);
    }

    /**
     * Gets query for [[AppraisalMasterForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasterForms() {
        return $this->hasMany(AppraisalMasterForm::className(), ['appraisal_master_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

}
