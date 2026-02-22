<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_form_punchlist".
 *
 * @property int $id
 * @property int|null $test_master_id
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefTestStatus $status0
 * @property TestDetailPunchlist[] $testDetailPunchlists
 * @property TestMaster $testMaster
 */
class TestFormPunchlist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_punchlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_master_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['test_master_id'], 'unique'],
            [['test_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestMaster::class, 'targetAttribute' => ['test_master_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestStatus::class, 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'test_master_id' => 'Test Master ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefTestStatus::class, ['id' => 'status']);
    }

    /**
     * Gets query for [[TestDetailPunchlists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailPunchlists() {
        return $this->hasMany(TestDetailPunchlist::class, ['form_punchlist_id' => 'id']);
    }

    /**
     * Gets query for [[TestMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMaster() {
        return $this->hasOne(TestMaster::class, ['id' => 'test_master_id']);
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

    public static function addForm($id) {
        $model = new TestFormPunchlist();
        $model->test_master_id = $id;
        $model->status = RefTestStatus::STS_READY_FOR_TESTING;
        return $model->save();
    }

}
