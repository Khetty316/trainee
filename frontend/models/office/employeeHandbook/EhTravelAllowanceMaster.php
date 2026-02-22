<?php

namespace frontend\models\office\employeeHandbook;

use Yii;
use common\models\User;

/**
 * This is the model class for table "eh_travel_allowance_master".
 *
 * @property int $id
 * @property int|null $eh_master_id
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property EmployeeHandbookMaster $ehMaster
 * @property User $createdBy
 * @property User $updatedBy
 */
class EhTravelAllowanceMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eh_travel_allowance_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eh_master_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['eh_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeHandbookMaster::className(), 'targetAttribute' => ['eh_master_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eh_master_id' => 'Eh Master ID',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[EhMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhMaster()
    {
        return $this->hasOne(EmployeeHandbookMaster::className(), ['id' => 'eh_master_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    
        public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }
}
