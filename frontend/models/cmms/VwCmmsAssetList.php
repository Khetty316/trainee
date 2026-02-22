<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "vw_cmms_asset_list".
 *
 * @property int $id
 * @property string|null $asset_area
 * @property string|null $asset_section
 * @property string|null $asset_code
 * @property string|null $manufacturer
 * @property string|null $serial_no
 * @property int|null $asset_fault_id
 * @property int|null $asset_list_id
 * @property string|null $asset_fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property int|null $is_deleted
 */
class VwCmmsAssetList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vw_cmms_asset_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'asset_fault_id', 'asset_list_id', 'is_deleted'], 'integer'],
            [['asset_area', 'asset_section', 'asset_code', 'manufacturer', 'serial_no', 'asset_fault_type', 'fault_primary_detail', 'fault_secondary_detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_area' => 'Asset Area',
            'asset_section' => 'Asset Section',
            'asset_code' => 'Asset Code',
            'manufacturer' => 'Manufacturer',
            'serial_no' => 'Serial No',
            'asset_fault_id' => 'Asset Fault ID',
            'asset_list_id' => 'Asset List ID',
            'asset_fault_type' => 'Asset Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
