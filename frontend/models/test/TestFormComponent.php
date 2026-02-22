<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_form_component".
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
 * @property TestDetailComponent[] $testDetailComponents
 * @property TestDetailConform[] $testDetailConforms
 * @property TestMaster $testMaster
 */
class TestFormComponent extends \yii\db\ActiveRecord {

    public $submitSts;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_component';
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
     * Gets query for [[TestDetailComponents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailComponents() {
        return $this->hasMany(TestDetailComponent::class, ['form_component_id' => 'id']);
    }

    /**
     * Gets query for [[TestDetailConforms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailConforms() {
        return $this->hasMany(TestDetailConform::class, ['form_component_id' => 'id']);
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
        $model = new TestFormComponent();
        $model->test_master_id = $id;
        $model->status = RefTestStatus::STS_SETUP;
        return $model->save();
    }

    public function copyOver($oldMasterId, $newMasterId) {
        $master = TestMaster::findOne($oldMasterId);
        $newform = new TestFormComponent();
        $newform->test_master_id = $newMasterId;
        $newform->status = RefTestStatus::STS_SETUP;
        if ($newform->save()) {
            $detail = new TestDetailComponent();
            return $detail->copyOver($master->testFormComponent->id, $newform->id);
        }
    }

}
