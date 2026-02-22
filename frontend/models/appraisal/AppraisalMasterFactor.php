<?php

namespace frontend\models\appraisal;

use Yii;
use frontend\models\appraisal\AppraisalMasterForm;
use frontend\models\common\RefAppraisalFactor;

/**
 * This is the model class for table "appraisal_master_factor".
 *
 * @property int $id
 * @property int|null $appraisal_master_form_id
 * @property int|null $factor_id
 * @property int|null $rating
 * @property int|null $review
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property AppraisalMasterForm $appraisalMasterForm
 * @property RefAppraisalFactor $factor
 */
class AppraisalMasterFactor extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'appraisal_master_factor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['appraisal_master_form_id', 'factor_id', 'rating', 'review', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['appraisal_master_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppraisalMasterForm::className(), 'targetAttribute' => ['appraisal_master_form_id' => 'id']],
            [['factor_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefAppraisalFactor::className(), 'targetAttribute' => ['factor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'appraisal_master_form_id' => 'Appraisal Master Form ID',
            'factor_id' => 'Factor ID',
            'rating' => 'Rating',
            'review' => 'Review',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[AppraisalMasterForm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasterForm() {
        return $this->hasOne(AppraisalMasterForm::className(), ['id' => 'appraisal_master_form_id']);
    }

    /**
     * Gets query for [[Factor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFactor() {
        return $this->hasOne(RefAppraisalFactor::className(), ['id' => 'factor_id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public static function getFactorAsArray($id) {
        return AppraisalMasterFactor::find()
                        ->select(['appraisal_master_factor.*', 'b.factor_name', 'b.factor_name_my', 'b.factor_desc', 'b.factor_desc_my'])
                        ->join('LEFT JOIN', ['b' => RefAppraisalFactor::tableName()], 'b.id = appraisal_master_factor.factor_id')
                        ->where(['appraisal_master_form_id' => $id])
                        ->asArray()->all();
    }

}
