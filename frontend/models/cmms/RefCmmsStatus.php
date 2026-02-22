<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_cmms_status".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $active_sts
 *
 * @property CmmsFaultList[] $cmmsFaultLists
 */
class RefCmmsStatus extends \yii\db\ActiveRecord
{
    public static $STATUS_SCREENING_AND_PRIORITISATION = 1;
    public static $STATUS_WORK_ORDER_CREATION = 2;
    public static $STATUS_EXECUTION_OF_CORRECTIVE_WORK = 3;
    public static $STATUS_VERIFICATION_AND_CLOSEOUT = 4;
    public static $STATUS_POST_REPAIR_FOLLOW_UP = 5;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_cmms_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
        return $this->hasMany(CmmsFaultList::className(), ['status' => 'id']);
    }
}
