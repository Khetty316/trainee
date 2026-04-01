<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_status_type".
 *
 * @property int $id
 * @property string|null $report_category
 * @property string|null $status_detail
 *
 * @property CmmsPreventiveMaintenanceDetails[] $cmmsPreventiveMaintenanceDetails
 */
class RefStatusType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_status_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_category', 'status_detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_category' => 'Report Category',
            'status_detail' => 'Status Detail',
        ];
    }

    /**
     * Gets query for [[CmmsPreventiveMaintenanceDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPreventiveMaintenanceDetails()
    {
        return $this->hasMany(CmmsPreventiveMaintenanceDetails::className(), ['status_type_id' => 'id']);
    }
}
