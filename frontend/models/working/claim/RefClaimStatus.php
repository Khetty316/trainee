<?php

namespace frontend\models\working\claim;

use Yii;

/**
 * This is the model class for table "ref_claim_status".
 *
 * @property string $code
 * @property string|null $status_name
 * @property int|null $step
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $update_by
 *
 * @property ClaimsMaster[] $claimsMasters
 */
class RefClaimStatus extends \yii\db\ActiveRecord {

    const STATUS_PAID = 4;
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_claim_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['step', 'created_by', 'update_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 5],
            [['status_name'], 'string', 'max' => 100],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'status_name' => 'Status Name',
            'step' => 'Step',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'update_by' => 'Update By',
        ];
    }

    /**
     * Gets query for [[ClaimsMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsMasters() {
        return $this->hasMany(ClaimsMaster::className(), ['claims_status' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefClaimStatus::find()->orderBy(['status_name' => SORT_ASC])->all(), "code", "status_name");
    }

}
