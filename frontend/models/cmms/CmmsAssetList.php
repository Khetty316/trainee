<?php

namespace frontend\models\cmms;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cmms_asset_list".
 *
 * @property int $id
 * @property string|null $area
 * @property string|null $section
 * @property string|null $name
 * @property string|null $manufacturer
 * @property int|null $part_id
 * @property string|null $serial_no
 * @property string|null $date_of_purchase
 * @property string|null $date_of_installation
 * @property int|null $active_sts
 * @property int|null $is_deleted
 * @property int|null $updated_by
 * @property string|null $asset_id
 *
 * @property CmmsAssetFaults[] $cmmsAssetFaults
 * @property CmmsPartList $part
 */
class CmmsAssetList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_asset_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'active_sts', 'is_deleted', 'updated_by'], 'integer'],
            [['date_of_purchase', 'date_of_installation'], 'safe'],
            [['area', 'section', 'name', 'manufacturer', 'serial_no', 'asset_id'], 'string', 'max' => 255],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPartList::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area' => 'Area',
            'section' => 'Section',
            'name' => 'Name',
            'manufacturer' => 'Manufacturer',
            'part_id' => 'Part ID',
            'serial_no' => 'Serial No',
            'date_of_purchase' => 'Date Of Purchase',
            'date_of_installation' => 'Date Of Installation',
            'active_sts' => 'Active Sts',
            'is_deleted' => 'Is Deleted',
            'updated_by' => 'Updated By',
            'asset_id' => 'Asset Code',
        ];
    }

    /**
     * Gets query for [[CmmsAssetFaults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetFaults()
    {
        return $this->hasMany(CmmsAssetFaults::className(), ['asset_list_id' => 'id']);
    }

    /**
     * Gets query for [[Part]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(CmmsPartList::className(), ['id' => 'part_id']);
    }
    
    public static function getAssetID($assetArea, $assetSection, $assetCode)
    {
        $assetListID = \frontend\models\cmms\CmmsAssetList::find()
                ->select('id')
                ->where([
                    'area' => $assetArea,
                    'section' => $assetSection,
                    'asset_id' => $assetCode,
                    'active_sts' => 1
                        ])
                ->scalar();
        return $assetListID;
    } 
    
    public static function getAreas() {
        $areas = self::find()
            ->select('area')
            ->where(['<>', 'area', ''])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();
        
        return array_unique(array_filter($areas));
    }
    
    public static function getAreas_by_Code($assetCode)
    {
        $areas = self::find()
            ->select('area')
            ->andWhere(['asset_id' => $assetCode])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();

        return array_unique(array_filter($areas));
    }
    
    public static function getSections() {
        $sections = self::find()
            ->select('section')
            ->where(['<>', 'section', ''])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();
        
        return array_unique(array_filter($sections));
    }
//    
    public static function getSections_by_Code_Area($assetCode, $assetArea)
    {
        $sections = self::find()
            ->select('section')
            ->where(['<>', 'section', ''])
            ->andWhere(['asset_id' => $assetCode])
            ->andWhere(['area' => $assetArea])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();

        return array_unique(array_filter($sections));
    }
    
    public static function getAssetCodes()
    {
        $assetCodes = self::find()
            ->select('asset_id')
            ->where(['<>', 'asset_id', ''])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();

        return array_unique(array_filter($assetCodes));
    }
    
    public static function getAssetNames()
    {
        $assetNames = self::find()
            ->select('name')
            ->where(['<>', 'name', ''])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['active_sts' => 1])
            ->column();

        return array_unique(array_filter($assetNames));
    }
    
    public static function getFaultType_by_ID($asset_ID)
    {
        return ArrayHelper::map(CmmsAssetFaults::find()
                ->select('fault_type')
                ->distinct()
                ->where([
                    'active_sts' => 1,
                    'asset_id' => $asset_ID,
                    'is_deleted' => 0
                ])
                ->andWhere(['IS NOT', 'fault_type', null])
                ->andWhere("TRIM(fault_type) <> ''")
                ->orderBy('fault_type')
                ->asArray()
                ->all(),
                'fault_type', 'fault_type'
        );
    }
    
    public static function getPrimaryFault_by_type($faultType)
    {
        return \yii\helpers\ArrayHelper::map(CmmsAssetFaults::find()
                ->select('fault_primary_detail')
                ->distinct()
                ->where([
                    'active_sts' => 1,
                    'fault_type' => $faultType,
                    'is_deleted' => 0
                ])
                ->andWhere(['IS NOT', 'fault_primary_detail', null])
                ->andWhere("TRIM(fault_primary_detail) <> ''")
                ->orderBy('fault_primary_detail')
                ->asArray()
                ->all(),
                'fault_primary_detail', 'fault_primary_detail'
                );
    }
    
    public static function getSecondaryFault($primaryFault)
    {
        return ArrayHelper::map(CmmsAssetFaults::find()
                ->select('fault_secondary_detail')
                ->where([
                    'active_sts' => 1,
                    'fault_primary_detail' => $primaryFault,
                    'is_deleted' => 0
                ])
                ->andWhere(['IS NOT', 'fault_secondary_detail', null])
                ->andWhere("TRIM(fault_secondary_detail) <> ''")
                ->orderBy('fault_secondary_detail')
                ->asArray()
                ->all(),
                'fault_secondary_detail', 'fault_secondary_detail'
        );
    }
}
