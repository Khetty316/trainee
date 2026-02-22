<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "preventive_maintenance_master".
 *
 * @property int $id
 * @property string|null $equipment_code
 * @property string|null $equipment_description
 * @property string|null $remark
 * @property string|null $next_service_date
 * @property string|null $created_at
 * @property int|null $created_by
 */
class PreventiveMaintenanceMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'preventive_maintenance_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
            [['next_service_date', 'created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['equipment_code'], 'string', 'max' => 10],
            [['equipment_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_code' => 'Equipment Code',
            'equipment_description' => 'Equipment Description',
            'remark' => 'Remark',
            'next_service_date' => 'Next Service Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
