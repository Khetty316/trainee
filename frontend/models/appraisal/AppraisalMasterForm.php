<?php

namespace frontend\models\appraisal;

use Yii;
use frontend\models\appraisal\AppraisalMaster;
use frontend\models\appraisal\AppraisalMasterFactor;
use frontend\models\common\RefAppraisalForm;

/**
 * This is the model class for table "appraisal_master_form".
 *
 * @property int $id
 * @property int|null $appraisal_master_id
 * @property int|null $form_id
 * @property int|null $subtotal_rating
 * @property int|null $final_subtotal_rating
 * @property int|null $subtotal_review
 * @property int|null $final_subtotal_review
 * @property int|null $fullmark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property AppraisalMasterFactor[] $appraisalMasterFactors
 * @property AppraisalMaster $appraisalMaster
 * @property RefAppraisalForm $form
 */
class AppraisalMasterForm extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'appraisal_master_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['appraisal_master_id', 'form_id', 'subtotal_rating', 'final_subtotal_rating', 'subtotal_review', 'final_subtotal_review', 'fullmark', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['appraisal_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppraisalMaster::className(), 'targetAttribute' => ['appraisal_master_id' => 'id']],
            [['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefAppraisalForm::className(), 'targetAttribute' => ['form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'appraisal_master_id' => 'Appraisal Master ID',
            'form_id' => 'Form ID',
            'subtotal_rating' => 'Subtotal Rating',
            'final_subtotal_rating' => 'Final Subtotal Rating',
            'subtotal_review' => 'Subtotal Review',
            'final_subtotal_review' => 'Final Subtotal Review',
            'fullmark' => 'Fullmark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[AppraisalMasterFactors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasterFactors() {
        return $this->hasMany(AppraisalMasterFactor::className(), ['appraisal_master_form_id' => 'id']);
    }

    /**
     * Gets query for [[AppraisalMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMaster() {
        return $this->hasOne(AppraisalMaster::className(), ['id' => 'appraisal_master_id']);
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm() {
        return $this->hasOne(RefAppraisalForm::className(), ['id' => 'form_id']);
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

    public static function getFormsAsArray($id) {
        return AppraisalMasterForm::find()
                        ->select(['appraisal_master_form.id as form_id', 'appraisal_master_form.*', 'b.form_name', 'b.form_name_my'])
                        ->leftJoin(['b' => RefAppraisalForm::tableName()], 'appraisal_master_form.form_id = b.id')
                        ->where(['appraisal_master_id' => $id])->asArray()->all();
    }

}
