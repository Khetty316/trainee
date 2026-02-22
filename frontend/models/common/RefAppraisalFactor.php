<?php

namespace frontend\models\common;

use Yii;
use frontend\models\appraisal\AppraisalMasterFactor;
use frontend\models\common\RefAppraisalForm;

/**
 * This is the model class for table "ref_appraisal_factor".
 *
 * @property int $id
 * @property int|null $form_id
 * @property string|null $factor_name
 * @property string|null $factor_name_my
 * @property string|null $factor_desc
 * @property string|null $factor_desc_my
 * @property int|null $is_active
 * @property int|null $order
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AppraisalMasterFactor[] $appraisalMasterFactors
 * @property RefAppraisalForm $form
 */
class RefAppraisalFactor extends \yii\db\ActiveRecord {

    const MAX_MARK_PER_FACTOR = 5;
    const FACTOR_OVERTIME = 18;
    const FACTOR_PUNCTUALITY = 33;
    const FACTOR_ATTENDANCE = 36;
    const MANUAL_PROCESSED_FACTORS = [self::FACTOR_OVERTIME, self::FACTOR_PUNCTUALITY, self::FACTOR_ATTENDANCE];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_appraisal_factor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_id', 'is_active', 'order', 'created_by'], 'integer'],
            [['factor_desc', 'factor_desc_my'], 'string'],
            [['created_at'], 'safe'],
            [['factor_name', 'factor_name_my'], 'string', 'max' => 255],
            [['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefAppraisalForm::className(), 'targetAttribute' => ['form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_id' => 'Form ID',
            'factor_name' => 'Factor Name',
            'factor_name_my' => 'Factor Name My',
            'factor_desc' => 'Factor Desc',
            'factor_desc_my' => 'Factor Desc My',
            'is_active' => 'Is Active',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AppraisalMasterFactors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasterFactors() {
        return $this->hasMany(AppraisalMasterFactor::className(), ['factor_id' => 'id']);
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm() {
        return $this->hasOne(RefAppraisalForm::className(), ['id' => 'form_id']);
    }

    public static function getSingleFormFactors($id) {
        return RefAppraisalFactor::find()->where(['form_id' => $id, 'is_active' => 1])->orderBy('order')->all();
    }

}
