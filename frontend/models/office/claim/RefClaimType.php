<?php

namespace frontend\models\office\claim;

use Yii;

/**
 * This is the model class for table "ref_claim_type".
 *
 * @property string $code
 * @property string|null $claim_name
 * @property string|null $claim_description
 * @property string|null $claim_shortform
 * @property string|null $claim_family
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $update_by
 * @property int $budget
 *
 * @property ClaimsMaster[] $claimsMasters
 */
class RefClaimType extends \yii\db\ActiveRecord {

    CONST codeAccommodation = "accom";
    CONST codeMaterial = "mat";
    CONST codeMeal = "meal";
    CONST codePetrol = "petrol";
    CONST codePetty = "petty";
    CONST codeTravel = "tra";
    CONST codeMedical = "med";
    CONST codeTelephone = "tele";
    CONST codeRepair = "repair";
    CONST codeDirector = "director";
    CONST codeExecOTMeal = "execotmeal";
    CONST codeProdOTMeal = "prodotmeal";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_claim_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['created_at', 'update_at'], 'safe'],
            [['created_by', 'update_by', 'budget'], 'integer'],
            [['code', 'claim_shortform', 'claim_family'], 'string', 'max' => 5],
            [['claim_name'], 'string', 'max' => 100],
            [['claim_description'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'claim_name' => 'Claim Name',
            'claim_description' => 'Claim Description',
            'claim_shortform' => 'Claim Shortform',
            'claim_family' => 'Claim Family',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'update_by' => 'Update By',
            'budget' => 'Budget',
        ];
    }

    /**
     * Gets query for [[ClaimsMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsMasters() {
        return $this->hasMany(ClaimsMaster::className(), ['claim_type' => 'code']);
    }

    public static function getDropDownList($grade = null) {
        if ($grade === \frontend\models\RefStaffGrade::EXEC_CODE) {
            return \yii\helpers\ArrayHelper::map(RefClaimType::find()->where(['is_active' => 1])->orderBy(['claim_name' => SORT_ASC])->all(), "code", "claim_name");
        } else {
            return \yii\helpers\ArrayHelper::map(RefClaimType::find()->where(['grade' => 0, 'is_active' => 1])->orderBy(['claim_name' => SORT_ASC])->all(), "code", "claim_name");
        }
    }

    public static function getClaimTypeforEntitlement() {
        return RefClaimType::find()->where(["budget" => 1])->orderBy("order")->asArray()->all();
    }
}
