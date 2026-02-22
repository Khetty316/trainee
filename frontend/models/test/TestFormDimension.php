<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_form_dimension".
 *
 * @property int $id
 * @property int|null $test_master_id
 * @property string|null $rev_no
 * @property string|null $doc_ref
 * @property string|null $template
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property float|null $treshold_a
 * @property float|null $treshold_b
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestDetailDimension[] $testDetailDimensions
 * @property TestMaster $testMaster
 * @property RefTestStatus $status0
 */
class TestFormDimension extends \yii\db\ActiveRecord {

    const THRESHOLD_A = 0.75;
    const THRESHOLD_B = 10;
    const MEASUREMENT_A_MIN = 0;
    const MEASUREMENT_A_MAX = 999;
    const MEASUREMENT_B_MIN = 1000;
    const MEASUREMENT_B_MAX = 8000;
    const RESULT_FAIL = ['value' => 0];
    const RESULT_PASS = ['value' => 1];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_dimension';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'template', 'status', 'created_at', 'created_by', 'treshold_a', 'treshold_b', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_master_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['template'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['treshold_a', 'treshold_b'], 'number'],
            [['rev_no', 'doc_ref'], 'string', 'max' => 255],
            [['test_master_id'], 'unique'],
            [['test_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestMaster::className(), 'targetAttribute' => ['test_master_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestStatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'test_master_id' => 'Test Master ID',
            'rev_no' => 'Revision No',
            'doc_ref' => 'Document Ref',
            'template' => 'Template',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'treshold_a' => 'Treshold A',
            'treshold_b' => 'Treshold B',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TestDetailDimensions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailDimensions() {
        return $this->hasMany(TestDetailDimension::className(), ['form_dimension_id' => 'id']);
    }

    /**
     * Gets query for [[TestMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMaster() {
        return $this->hasOne(TestMaster::className(), ['id' => 'test_master_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefTestStatus::className(), ['id' => 'status']);
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
        $model = new TestFormDimension();
        $model->test_master_id = $id;
        $data = TestTemplate::find()->where(['formcode' => TestMaster::CODE_DIMENSION, 'active_sts' => 1])->one();
//        $model->template = $data ? $data['proctest1'] : null;
        if ($data) {
            if (empty(trim(strip_tags($data['proctest1'])))) {
                $model->template = null;
            } else {
                $testTemplate = new TestTemplate();
                $newhtml = $testTemplate->cleanHtmlContent($data['proctest1']);
                $model->template = preg_replace('/<(\w+)(\s*[^>]*)><\/\1>/', '', $newhtml);
            }
        } else {
            $model->template = null;
        }
        $model->rev_no = $data->rev_no ?? null;
        $model->doc_ref = $data->doc_ref ?? null;
        $model->treshold_a = TestFormDimension::THRESHOLD_A;
        $model->treshold_b = TestFormDimension::THRESHOLD_B;
        $model->status = RefTestStatus::STS_SETUP;
        return $model->save();
    }

    public function customProcedures($data, $templateAttribute, $inPDF) {
        if (!$templateAttribute) {
            $procedures = $this->setThreshold($data['proctest1'], TestFormDimension::THRESHOLD_A, TestFormDimension::THRESHOLD_B, $inPDF);
        } else {
            $testTemplate = explode('|', $data->template);
            $procedures = $this->setThreshold($testTemplate[0], $data->treshold_a, $data->treshold_b, $inPDF);
        }

        return $procedures;
    }

    public function setThreshold($data, $thresholdA, $thresholdB, $inPDF) {
        $replacements = [
            '&plusmn;{{value1}}' => $thresholdA,
            '&plusmn;{{value2}}' => $thresholdB,
        ];

        $replaceValue = array_map(function ($value) {
            return "&plusmn;$value";
        }, $replacements);

        if ($inPDF) {
            $replaceValue = array_map(function ($value) {
                return '<span style="color: #000; font-weight: normal;">±' . $value . '</span>';
            }, $replacements);
        }

        $value = str_replace(array_keys($replacements), $replaceValue, $data);
        return $value;
    }

    public function copyOver($oldMasterId, $newMasterId) {
        $master = TestMaster::findOne($oldMasterId);
        $oldForm = $master->testFormDimension;
        $newform = new TestFormDimension();
        $newform->test_master_id = $newMasterId;
        $newform->template = $oldForm->template;
        $newform->treshold_a = $oldForm->treshold_a;
        $newform->treshold_b = $oldForm->treshold_b;
        $newform->status = RefTestStatus::STS_SETUP;
        if ($newform->save()) {
            $detail = new TestDetailDimension();
            return $detail->copyOver($master->testFormDimension->id, $newform->id);
        }
    }

}
