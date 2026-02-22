<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_approval_status".
 *
 * @property string $code
 * @property string $description
 * @property int|null $order
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster[] $assetMasters
 */
class RefAssetApprovalStatus extends \yii\db\ActiveRecord {

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVE = 'approve';
    const STATUS_CANCEL = 'cancel';
    const STATUS_REJECT = 'reject';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_asset_approval_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'description'], 'required'],
            [['order', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 15],
            [['description'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'description' => 'Description',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AssetMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetMasters() {
        return $this->hasMany(AssetMaster::className(), ['approval_status' => 'code']);
    }

}
