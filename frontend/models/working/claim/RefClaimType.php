<?php

namespace frontend\models\working\claim;

use Yii;

/**
 * This is the model class for table "ref_claim_type".
 *
 * @property string $code
 * @property string $claim_name
 * @property string|null $claim_description
 * @property string|null $claim_shortform
 * @property string $claim_family
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $update_by
 *
 * @property ClaimsDetail[] $claimsDetails
 * @property ClaimsMaster[] $claimsMasters
 */
class RefClaimType extends \yii\db\ActiveRecord {

    const TYPE_ENTERTAINMENT = "ent";
    const TYPE_MEDICAL = "med";
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
            [['code', 'claim_name', 'claim_family'], 'required'],
            [['created_at', 'update_at'], 'safe'],
            [['created_by', 'update_by'], 'integer'],
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
        ];
    }

    /**
     * Gets query for [[ClaimsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsDetails() {
        return $this->hasMany(ClaimsDetail::className(), ['claim_type' => 'code']);
    }

    /**
     * Gets query for [[ClaimsMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsMasters() {
        return $this->hasMany(ClaimsMaster::className(), ['claim_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefClaimType::find()->orderBy(['claim_name' => SORT_ASC])->all(), "code", "claim_name");
    }
    
    public static function getDropDownListNoTravel() {
        return \yii\helpers\ArrayHelper::map(RefClaimType::find()->where('code!="tra"')->orderBy(['claim_name' => SORT_ASC])->all(), "code", "claim_name");
    }

}
