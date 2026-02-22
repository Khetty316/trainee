<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_cmms_fault_details".
 *
 * @property int $id
 * @property string|null $area
 * @property string|null $section
 * @property string|null $asset_code
 * @property int|null $active_sts
 *
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property CmmsFaultListDetails[] $cmmsFaultListDetails
 */
class RefCmmsFaultDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_cmms_fault_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts'], 'integer'],
            [['area', 'section', 'asset_code'], 'string', 'max' => 255],
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
            'asset_code' => 'Asset Code',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['fault_detail_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsFaultListDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultListDetails()
    {
        return $this->hasMany(CmmsFaultListDetails::className(), ['fault_detail_ref_id' => 'id']);
    }
    
    public static function getActiveDropdownlist() 
    {
        return \yii\helpers\ArrayHelper::map(RefCmmsFaultDetails::findAll(["active_sts" => "1"]), "area", "area");
    }
    
    public static function getSections() {
        return \yii\helpers\ArrayHelper::map(RefCmmsFaultDetails::find()
                ->where([
                    "active_sts" => 1,
                    ])->all(),
                'section', 'section');
    }
    
    public static function getSections_by_Area($area) 
    {
        return \yii\helpers\ArrayHelper::map(RefCmmsFaultDetails::find()
                ->where([
                    'active_sts' => 1,
                    'area' => $area,
                ])
                ->all(),
                'section', 'section'
        );
    }
}
