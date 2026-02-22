<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_status".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $badge
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestMaster[] $testMasters
 */
class RefTestStatus extends \yii\db\ActiveRecord {

    const STS_SETUP = 2;
    const STS_READY_FOR_TESTING = 3;
    const STS_IN_TESTING = 4;
    const STS_FAIL = 5;
    const STS_COMPLETE = 6;
    const STS_ALL = [self::STS_SETUP, self::STS_READY_FOR_TESTING, self::STS_IN_TESTING, self::STS_FAIL, self::STS_COMPLETE];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_test_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'badge', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['name', 'badge'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'badge' => 'Badge',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TestMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMasters() {
        return $this->hasMany(TestMaster::class, ['status' => 'id']);
    }

    public static function getDropDownListFiltered() {
        $filteredData = RefTestStatus::find()->all();

        return \yii\helpers\ArrayHelper::map($filteredData, "badge", "name");
    }

}
