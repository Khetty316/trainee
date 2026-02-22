<?php

namespace frontend\models\appraisal;

use Yii;
use common\models\User;
use frontend\models\appraisal\AppraisalMaster;
use frontend\models\common\RefAppraisalStatus;

/**
 * This is the model class for table "appraisal_main".
 *
 * @property int $id
 * @property string|null $index
 * @property string|null $description
 * @property int|null $status
 * @property int|null $year
 * @property string|null $appraisal_start_date
 * @property string|null $appraisal_end_date
 * @property string|null $rating_end_date
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property AppraisalMaster[] $appraisalMasters
 * @property User $createdBy
 * @property RefAppraisalStatus $status0
 */
class AppraisalMain extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'appraisal_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['description', 'appraisal_start_date', 'appraisal_end_date', 'rating_end_date'], 'required'],
            [['index', 'description', 'status', 'year', 'appraisal_start_date', 'appraisal_end_date', 'rating_end_date', 'created_at', 'created_by'], 'default', 'value' => null],
            [['description'], 'string'],
            [['status', 'year', 'created_by'], 'integer'],
            [['status', 'appraisal_start_date', 'appraisal_end_date', 'rating_end_date', 'created_at'], 'safe'],
            [['index'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefAppraisalStatus::class, 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'index' => 'Index',
            'description' => 'Description',
            'status' => 'Status',
            'year' => 'Year',
            'appraisal_start_date' => 'Appraisal Start Date',
            'appraisal_end_date' => 'Appraisal End Date',
            'rating_end_date' => 'Rating End Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AppraisalMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasters() {
        return $this->hasMany(AppraisalMaster::class, ['main_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefAppraisalStatus::class, ['id' => 'status']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

    public static function getMainIndex($year) {
        $mains = AppraisalMain::findAll(['year' => $year]);

        foreach ($mains as $main) {
            if (!$main->appraisalMasters) {
                $main->delete();
            }
        }

        $mainNumber = AppraisalMain::find()->where(['year' => $year])->count();
        $count = $mainNumber + 1;

        $count = str_pad($count, 2, '0', STR_PAD_LEFT);

        return "AP$year-$count";
    }

    public static function updateStatuses($id) {
        $main = AppraisalMain::findOne($id);

        $masters = AppraisalMaster::findAll(['main_id' => $id]);
        $complete = true;
        $status = true;
        foreach ($masters as $master) {
            if ($master->appraisal_sts != RefAppraisalStatus::STS_COMPLETE) {
                $complete = false;
            }
            if ($master->appraisal_sts < RefAppraisalStatus::STS_WAIT_REVIEW) {
                $status = false;
            }
        }

        if ($complete) {
            $main->status = RefAppraisalStatus::STS_COMPLETE;
        } else if ($status) {
            $main->status = RefAppraisalStatus::STS_MAIN_REVIEW_PROCESS;
        } else if (!$status) {
            $main->status = RefAppraisalStatus::STS_MAIN_IN_PROCESS;
        }

        $main->update(false);
        return;
    }

}
