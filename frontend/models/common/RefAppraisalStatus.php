<?php

namespace frontend\models\common;

use Yii;
use common\models\User;
use frontend\models\appraisal\AppraisalMain;
use frontend\models\appraisal\AppraisalMaster;

/**
 * This is the model class for table "ref_appraisal_status".
 *
 * @property int $id
 * @property string|null $sts_name
 * @property int|null $is_active
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property AppraisalMain[] $appraisalMains
 * @property AppraisalMaster[] $appraisalMasters
 * @property User $createdBy
 */
class RefAppraisalStatus extends \yii\db\ActiveRecord {

    const STS_WAIT_RATING = 1;
    const STS_RATED_NOT_CONFIRMED = 2;
    const STS_WAIT_REVIEW = 3;
    const STS_REVIEWED_NOT_CONFIRMED = 4;
    const STS_COMPLETE = 5;
    const STS_MAIN_IN_PROCESS = 6;
    const STS_MAIN_REVIEW_PROCESS = 7;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_appraisal_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['is_active', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['sts_name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sts_name' => 'Sts Name',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AppraisalMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMains() {
        return $this->hasMany(AppraisalMain::className(), ['status' => 'id']);
    }

    /**
     * Gets query for [[AppraisalMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasters() {
        return $this->hasMany(AppraisalMaster::className(), ['appraisal_sts' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public static function getDropDownListMaster() {
        return \yii\helpers\ArrayHelper::map(
                        RefAppraisalStatus::find()
                                ->where(['and', ['<>', 'id', RefAppraisalStatus::STS_MAIN_IN_PROCESS], ['<>', 'id', RefAppraisalStatus::STS_MAIN_REVIEW_PROCESS]])
                                ->andWhere(['is_active' => 1])
                                ->orderBy(['sts_name' => SORT_ASC])
                                ->all(), "sts_name", "sts_name");
    }

    public static function getDropDownListMain() {
        return \yii\helpers\ArrayHelper::map(
                        RefAppraisalStatus::find()
                                ->andWhere(['<>', 'id', RefAppraisalStatus::STS_WAIT_RATING])
                                ->andWhere(['<>', 'id', RefAppraisalStatus::STS_RATED_NOT_CONFIRMED])
                                ->andWhere(['<>', 'id', RefAppraisalStatus::STS_WAIT_REVIEW])
                                ->andWhere(['<>', 'id', RefAppraisalStatus::STS_REVIEWED_NOT_CONFIRMED])
                                ->andWhere(['is_active' => 1])
                                ->orderBy(['sts_name' => SORT_ASC])
                                ->all(), "sts_name", "sts_name");
    }

}
