<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_machine_breakdown_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property int|null $active_sts
 *
 * @property CmmsFaultListDetails[] $cmmsFaultListDetails
 */
class RefMachineBreakdownType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_machine_breakdown_type';
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
     * Gets query for [[CmmsFaultListDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultListDetails()
    {
        return $this->hasMany(CmmsFaultListDetails::className(), ['machine_breakdown_type_id' => 'id']);
    }
    
    public static function getMachineBreakdownTypeActiveDropdownlist() {
        return \yii\helpers\ArrayHelper::map(RefMachineBreakdownType::findAll(["active_sts" => "1"]), "name", "name");
    }
    
    public static function getActiveDropdownlist_by_id() {
        return \yii\helpers\ArrayHelper::map(RefMachineBreakdownType::findAll(["active_sts" => "1"]), "id", "name");
    }
}
