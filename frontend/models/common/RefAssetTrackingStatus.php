<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_tracking_status".
 *
 * @property string $code
 * @property string $description
 * @property int|null $order
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetTracking[] $assetTrackings
 */
class RefAssetTrackingStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_asset_tracking_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'description' => 'Description',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AssetTrackings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetTrackings()
    {
        return $this->hasMany(AssetTracking::className(), ['request_status' => 'code']);
    }
}
