<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_progress_status".
 *
 * @property int $id
 * @property int|null $active_sts
 * @property string|null $name
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 */
class RefProgressStatus extends \yii\db\ActiveRecord
{ 
    public static $STATUS_ASSIGNED = 1;
    public static $STATUS_IN_PROGRESS = 2;
    public static $STATUS_COMPLETED = 3;
    public static $STATUS_CLOSED = 4;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_progress_status';
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
            'active_sts' => 'Active Sts',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['progress_status_id' => 'id']);
    }
}
