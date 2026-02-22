<?php

namespace frontend\models\office\claim;

use Yii;

/**
 * This is the model class for table "ref_claim_status".
 *
 * @property int $id
 * @property string|null $status_name
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $update_by
 *
 * @property ClaimsMaster[] $claimsMasters
 */
class RefClaimStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_claim_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'update_by'], 'integer'],
            [['status_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_name' => 'Status Name',
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
    public function getClaimsMasters()
    {
        return $this->hasMany(ClaimsMaster::className(), ['claims_status' => 'id']);
    }
}
