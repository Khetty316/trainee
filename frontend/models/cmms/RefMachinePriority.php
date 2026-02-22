<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_machine_priority".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property int|null $active_sts
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 * @property CmmsFaultListDetails[] $cmmsFaultListDetails
 */
class RefMachinePriority extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_machine_priority';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['machine_priority_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsFaultListDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultListDetails()
    {
        return $this->hasMany(CmmsFaultListDetails::className(), ['machine_priority_id' => 'id']);
    }
    
    public static function getMachinePriorityActiveDDropdownlist() {
        return \yii\helpers\ArrayHelper::map(RefMachinePriority::findAll(["active_sts" => "1"]), "name", "name");
    }
    
    public static function getActiveDropdownlist_by_id() {
        return \yii\helpers\ArrayHelper::map(RefMachinePriority::findAll(["active_sts" => "1"]), "id", "name");
    }
}
