<?php

namespace frontend\models\covid\testkit;

use Yii;
use common\models\User;
/**
 * This is the model class for table "covid_testkit_inventory".
 *
 * @property int $id
 * @property string $brand
 * @property string|null $record_date
 * @property int $total_movement
 * @property string|null $remark
 * @property int|null $giving_to to whom the kit is given
 * @property int $confirm_status
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $givingTo
 * @property CovidTestkitRecord[] $covidTestkitRecords
 */
class CovidTestkitInventory extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'covid_testkit_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['brand', 'giving_to', 'total_movement'], 'required'],
            [['record_date', 'created_at'], 'safe'],
            [['total_movement', 'giving_to', 'confirm_status', 'created_by'], 'integer'],
            [['brand', 'remark'], 'string', 'max' => 255],
            [['giving_to'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giving_to' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'brand' => 'Brand',
            'record_date' => 'Record Date',
            'total_movement' => 'Total Movement',
            'remark' => 'Remark',
            'giving_to' => 'Giving To',
            'confirm_status' => 'Confirm Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[GivingTo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGivingTo() {
        return $this->hasOne(User::className(), ['id' => 'giving_to']);
    }

    /**
     * Gets query for [[CovidTestkitRecords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCovidTestkitRecords() {
        return $this->hasMany(CovidTestkitRecord::className(), ['inventory_id' => 'id']);
    }

}
