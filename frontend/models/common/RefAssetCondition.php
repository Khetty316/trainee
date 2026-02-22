<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_condition".
 *
 * @property string $code
 * @property string|null $description
 * @property int|null $order
 * @property int $in_transfer
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster[] $assetMasters
 * @property AssetTracking[] $assetTrackings
 */
class RefAssetCondition extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_asset_condition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['order', 'in_transfer', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 15],
            [['description'], 'string', 'max' => 200],
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
            'in_transfer' => 'In Transfer',
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
        return $this->hasMany(AssetMaster::className(), ['condition' => 'code']);
    }

    /**
     * Gets query for [[AssetTrackings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetTrackings() {
        return $this->hasMany(AssetTracking::className(), ['receive_condition' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefAssetCondition::find()->orderBy(['order' => SORT_ASC])->all(), "code", "description");
    }
    public static function getDropDownListInTransfer() {
        return \yii\helpers\ArrayHelper::map(RefAssetCondition::find()->where(['in_transfer'=>'1'])->orderBy(['order' => SORT_ASC])->all(), "code", "description");
    }

}
