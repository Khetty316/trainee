<?php

namespace frontend\models\appraisal;

use Yii;
use common\models\VUser;

/**
 * This is the model class for table "v_appraisal_master".
 *
 * @property int $id
 * @property int|null $main_id
 * @property string|null $index
 * @property string|null $description
 * @property int|null $status
 * @property string|null $appraisal_start_date
 * @property string|null $appraisal_end_date
 * @property string|null $rating_end_date
 * @property string|null $status_name
 * @property int|null $user_id
 * @property string|null $staff_id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $fullname
 * @property string|null $date_of_join
 * @property string|null $design_name
 * @property string|null $staff_type
 * @property string|null $employment_type_code
 * @property string|null $employment_type
 * @property int|null $overall_rating
 * @property int|null $overall_review
 * @property int|null $appraisal_sts
 * @property string|null $appraisal_sts_name
 * @property string|null $appraise_by
 * @property string|null $appraise_date
 * @property int|null $review_by
 * @property string|null $review_by_name
 * @property string|null $review_date
 * @property string|null $staff_remark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class VAppraisalMaster extends \yii\db\ActiveRecord {

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_appraisal_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['main_id', 'index', 'description', 'status', 'appraisal_start_date', 'appraisal_end_date', 'rating_end_date', 'status_name', 'user_id', 'staff_id', 'username', 'email', 'fullname', 'date_of_join', 'design_name', 'staff_type', 'employment_type_code', 'employment_type', 'overall_rating', 'overall_review', 'appraisal_sts', 'appraisal_sts_name', 'appraise_by', 'appraise_date', 'review_by', 'review_by_name', 'review_date', 'staff_remark', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['id'], 'default', 'value' => 0],
            [['id', 'main_id', 'status', 'user_id', 'overall_rating', 'overall_review', 'appraisal_sts', 'review_by', 'created_by', 'updated_by'], 'integer'],
            [['description', 'staff_remark'], 'string'],
            [['appraisal_start_date', 'appraisal_end_date', 'rating_end_date', 'date_of_join', 'appraise_date', 'review_date', 'created_at', 'updated_at'], 'safe'],
            [['index', 'status_name', 'username', 'email', 'fullname', 'design_name', 'employment_type', 'appraisal_sts_name', 'appraise_by', 'review_by_name'], 'string', 'max' => 255],
            [['staff_id', 'employment_type_code'], 'string', 'max' => 10],
            [['staff_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'main_id' => 'Main ID',
            'index' => 'Index',
            'description' => 'Description',
            'status' => 'Status',
            'appraisal_start_date' => 'Appraisal Start Date',
            'appraisal_end_date' => 'Appraisal End Date',
            'rating_end_date' => 'Rating End Date',
            'status_name' => 'Status',
            'user_id' => 'User ID',
            'staff_id' => 'Staff ID',
            'username' => 'Username',
            'email' => 'Email',
            'fullname' => 'Fullname',
            'date_of_join' => 'Date Of Join',
            'design_name' => 'Designation',
            'staff_type' => 'Staff Type',
            'employment_type_code' => 'Employment Type Code',
            'employment_type' => 'Employment Type',
            'overall_rating' => 'Overall Rating',
            'overall_review' => 'Overall Review',
            'appraisal_sts' => 'Appraisal Status',
            'appraisal_sts_name' => 'Appraisal Status',
            'appraise_by' => 'Appraised By',
            'appraise_date' => 'Appraised On',
            'review_by' => 'Reviewed By',
            'review_by_name' => 'Reviewed By',
            'review_date' => 'Reviewed On',
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
    public function getVuser() {
        return $this->hasOne(VUser::className(), ['id' => 'user_id']);
    }

}
