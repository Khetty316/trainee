<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_asset_faults".
 *
 * @property int $id
 * @property int|null $asset_list_id
 * @property string|null $fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property int|null $active_sts
 * @property int|null $is_deleted
 * @property int|null $updated_by
 *
 * @property CmmsAssetList $asset
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property CmmsFaultListDetails[] $cmmsFaultListDetails
 * @property CmmsPartList[] $cmmsPartLists
 */
class CmmsAssetFaults extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_asset_faults';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asset_list_id', 'active_sts', 'is_deleted', 'updated_by'], 'integer'],
            [['fault_type', 'fault_primary_detail', 'fault_secondary_detail', 'asset_id'], 'string', 'max' => 255],
            [['asset_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetList::className(), 'targetAttribute' => ['asset_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_list_id' => 'Asset ID',
            'fault_type' => 'Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
            'active_sts' => 'Active Sts',
            'is_deleted' => 'Is Deleted',
            'updated_by' => 'Updated By',
            'asset_id' => 'Asset Code',
        ];
    }

    /**
     * Gets query for [[Asset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(CmmsAssetList::className(), ['id' => 'asset_list_id']);
    }

    /**
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['asset_list_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsFaultListDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultListDetails()
    {
        return $this->hasMany(CmmsFaultListDetails::className(), ['cmms_asset_list_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsPartLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPartLists()
    {
        return $this->hasMany(CmmsPartList::className(), ['asset_list_id' => 'id']);
    }
    
    public static function getFaultTypes()
    {
        $faultTypes = self::find()
            ->select('fault_type')
            ->where(['<>', 'fault_type', ''])
            ->andWhere(['active_sts' => 1])
            ->column();

        return array_unique(array_filter($faultTypes));
    }
}
