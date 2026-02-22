<?php

namespace frontend\models\common;

use Yii;
use frontend\models\appraisal\AppraisalMasterForm;
use frontend\models\common\RefAppraisalFactor;

/**
 * This is the model class for table "ref_appraisal_form".
 *
 * @property int $id
 * @property string|null $form_name
 * @property string|null $form_name_my
 * @property string|null $remark
 * @property int|null $is_active
 * @property int|null $order
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AppraisalMasterForm[] $appraisalMasterForms
 * @property RefAppraisalFactor[] $refAppraisalFactors
 */
class RefAppraisalForm extends \yii\db\ActiveRecord {

    const FORM_LIST = [1, 2, 3, 4, 5, 6, 7];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_appraisal_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['is_active', 'order', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['form_name', 'form_name_my', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_name' => 'Form Name',
            'form_name_my' => 'Form Name My',
            'remark' => 'Remark',
            'is_active' => 'Is Active',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AppraisalMasterForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasterForms() {
        return $this->hasMany(AppraisalMasterForm::className(), ['form_id' => 'id']);
    }

    /**
     * Gets query for [[RefAppraisalFactors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefAppraisalFactors() {
        return $this->hasMany(RefAppraisalFactor::className(), ['form_id' => 'id']);
    }

}
