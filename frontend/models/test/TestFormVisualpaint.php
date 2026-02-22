<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\RefTestStatus;

/**
 * This is the model class for table "test_form_visualpaint".
 *
 * @property int $id
 * @property int|null $test_master_id
 * @property string|null $rev_no
 * @property string|null $doc_ref
 * @property string|null $template
 * @property int|null $status
 * @property float|null $treshold_a
 * @property int|null $a_scratch
 * @property int|null $a_rust
 * @property string|null $a_color
 * @property string|null $a_finishing
 * @property string|null $a_remark
 * @property int|null $b_scratch
 * @property int|null $b_rust
 * @property string|null $b_color
 * @property string|null $b_finishing
 * @property string|null $b_remark
 * @property int|null $c_scratch
 * @property int|null $c_rust
 * @property string|null $c_color
 * @property string|null $c_finishing
 * @property string|null $c_remark
 * @property int|null $d_scratch
 * @property int|null $d_rust
 * @property string|null $d_color
 * @property string|null $d_finishing
 * @property string|null $d_remark
 * @property int|null $e_scratch
 * @property int|null $e_rust
 * @property string|null $e_color
 * @property string|null $e_finishing
 * @property string|null $e_remark
 * @property int|null $f_scratch
 * @property int|null $f_rust
 * @property string|null $f_color
 * @property string|null $f_finishing
 * @property string|null $f_remark
 * @property int|null $res_a
 * @property int|null $res_b
 * @property int|null $res_c
 * @property int|null $res_d
 * @property int|null $res_e
 * @property int|null $res_f
 * @property int|null $a_measure1
 * @property int|null $a_measure2
 * @property int|null $a_measure3
 * @property float|null $a_average
 * @property int|null $b_measure1
 * @property int|null $b_measure2
 * @property int|null $b_measure3
 * @property float|null $b_average
 * @property int|null $c_measure1
 * @property int|null $c_measure2
 * @property int|null $c_measure3
 * @property float|null $c_average
 * @property int|null $d_measure1
 * @property int|null $d_measure2
 * @property int|null $d_measure3
 * @property float|null $d_average
 * @property int|null $e_measure1
 * @property int|null $e_measure2
 * @property int|null $e_measure3
 * @property float|null $e_average
 * @property int|null $f_measure1
 * @property int|null $f_measure2
 * @property int|null $f_measure3
 * @property float|null $f_average
 * @property int|null $res_ave_a
 * @property int|null $res_ave_b
 * @property int|null $res_ave_c
 * @property int|null $res_ave_d
 * @property int|null $res_ave_e
 * @property int|null $res_ave_f
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestMaster $testMaster
 * @property RefTestStatus $status0
 */
class TestFormVisualpaint extends \yii\db\ActiveRecord {

    public $submitSts;

    const COLOR_RAL7032 = 'RAL7032 Beige Grey';
    const COLOR_RAL7034 = 'RAL7034 Light Grey';
    const COLOR_TKWHITE = 'White';
    const COLOR_TYPE = [self::COLOR_RAL7032 => self::COLOR_RAL7032, self::COLOR_RAL7034 => self::COLOR_RAL7034, self::COLOR_TKWHITE => self::COLOR_TKWHITE];
    const FINISHING_TYPE = ['Gloss' => 'Gloss', 'Matte' => 'Matte', 'Wrinkled' => 'Wrinkled'];
    const THRESHOLD_A = 30;
    const RESULT_FAIL = ['value' => 0];
    const RESULT_PASS = ['value' => 1];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_visualpaint';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'template', 'status', 'treshold_a', 'a_scratch', 'a_rust', 'a_color', 'a_finishing', 'a_remark', 'b_scratch', 'b_rust', 'b_color', 'b_finishing', 'b_remark', 'c_scratch', 'c_rust', 'c_color', 'c_finishing', 'c_remark', 'd_scratch', 'd_rust', 'd_color', 'd_finishing', 'd_remark', 'e_scratch', 'e_rust', 'e_color', 'e_finishing', 'e_remark', 'f_scratch', 'f_rust', 'f_color', 'f_finishing', 'f_remark', 'res_a', 'res_b', 'res_c', 'res_d', 'res_e', 'res_f', 'a_measure1', 'a_measure2', 'a_measure3', 'a_average', 'b_measure1', 'b_measure2', 'b_measure3', 'b_average', 'c_measure1', 'c_measure2', 'c_measure3', 'c_average', 'd_measure1', 'd_measure2', 'd_measure3', 'd_average', 'e_measure1', 'e_measure2', 'e_measure3', 'e_average', 'f_measure1', 'f_measure2', 'f_measure3', 'f_average', 'res_ave_a', 'res_ave_b', 'res_ave_c', 'res_ave_d', 'res_ave_e', 'res_ave_f', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_master_id', 'status', 'a_scratch', 'a_rust', 'b_scratch', 'b_rust', 'c_scratch', 'c_rust', 'd_scratch', 'd_rust', 'e_scratch', 'e_rust', 'f_scratch', 'f_rust', 'res_a', 'res_b', 'res_c', 'res_d', 'res_e', 'res_f', 'a_measure1', 'a_measure2', 'a_measure3', 'b_measure1', 'b_measure2', 'b_measure3', 'c_measure1', 'c_measure2', 'c_measure3', 'd_measure1', 'd_measure2', 'd_measure3', 'e_measure1', 'e_measure2', 'e_measure3', 'f_measure1', 'f_measure2', 'f_measure3', 'res_ave_a', 'res_ave_b', 'res_ave_c', 'res_ave_d', 'res_ave_e', 'res_ave_f', 'created_by', 'updated_by'], 'integer'],
            [['template'], 'string'],
            [['treshold_a', 'a_average', 'b_average', 'c_average', 'd_average', 'e_average', 'f_average'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['rev_no', 'doc_ref', 'a_color', 'a_finishing', 'a_remark', 'b_color', 'b_finishing', 'b_remark', 'c_color', 'c_finishing', 'c_remark', 'd_color', 'd_finishing', 'd_remark', 'e_color', 'e_finishing', 'e_remark', 'f_color', 'f_finishing', 'f_remark'], 'string', 'max' => 255],
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
            'doc_ref' => 'Document Reference',
            'template' => 'Template',
            'status' => 'Status',
            'treshold_a' => 'Treshold A',
            'a_scratch' => 'A Scratch',
            'a_rust' => 'A Rust',
            'a_color' => 'A Color',
            'a_finishing' => 'A Finishing',
            'a_remark' => 'A Remark',
            'b_scratch' => 'B Scratch',
            'b_rust' => 'B Rust',
            'b_color' => 'B Color',
            'b_finishing' => 'B Finishing',
            'b_remark' => 'B Remark',
            'c_scratch' => 'C Scratch',
            'c_rust' => 'C Rust',
            'c_color' => 'C Color',
            'c_finishing' => 'C Finishing',
            'c_remark' => 'C Remark',
            'd_scratch' => 'D Scratch',
            'd_rust' => 'D Rust',
            'd_color' => 'D Color',
            'd_finishing' => 'D Finishing',
            'd_remark' => 'D Remark',
            'e_scratch' => 'E Scratch',
            'e_rust' => 'E Rust',
            'e_color' => 'E Color',
            'e_finishing' => 'E Finishing',
            'e_remark' => 'E Remark',
            'f_scratch' => 'F Scratch',
            'f_rust' => 'F Rust',
            'f_color' => 'F Color',
            'f_finishing' => 'F Finishing',
            'f_remark' => 'F Remark',
            'res_a' => 'Res A',
            'res_b' => 'Res B',
            'res_c' => 'Res C',
            'res_d' => 'Res D',
            'res_e' => 'Res E',
            'res_f' => 'Res F',
            'a_measure1' => 'A Measure1',
            'a_measure2' => 'A Measure2',
            'a_measure3' => 'A Measure3',
            'a_average' => 'A Average',
            'b_measure1' => 'B Measure1',
            'b_measure2' => 'B Measure2',
            'b_measure3' => 'B Measure3',
            'b_average' => 'B Average',
            'c_measure1' => 'C Measure1',
            'c_measure2' => 'C Measure2',
            'c_measure3' => 'C Measure3',
            'c_average' => 'C Average',
            'd_measure1' => 'D Measure1',
            'd_measure2' => 'D Measure2',
            'd_measure3' => 'D Measure3',
            'd_average' => 'D Average',
            'e_measure1' => 'E Measure1',
            'e_measure2' => 'E Measure2',
            'e_measure3' => 'E Measure3',
            'e_average' => 'E Average',
            'f_measure1' => 'F Measure1',
            'f_measure2' => 'F Measure2',
            'f_measure3' => 'F Measure3',
            'f_average' => 'F Average',
            'res_ave_a' => 'Res Ave A',
            'res_ave_b' => 'Res Ave B',
            'res_ave_c' => 'Res Ave C',
            'res_ave_d' => 'Res Ave D',
            'res_ave_e' => 'Res Ave E',
            'res_ave_f' => 'Res Ave F',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->status == RefTestStatus::STS_COMPLETE || $this->status == RefTestStatus::STS_FAIL) {
            $attributesToValidate = [
                'a_scratch', 'a_rust', 'a_color', 'a_finishing', 'b_scratch', 'b_rust', 'b_color', 'b_finishing', 'c_scratch', 'c_rust', 'c_color', 'c_finishing',
                'd_scratch', 'd_rust', 'd_color', 'd_finishing', 'e_scratch', 'e_rust', 'e_color', 'e_finishing', 'f_scratch', 'f_rust', 'f_color', 'f_finishing',
                'a_measure1', 'a_measure2', 'a_measure3', 'b_measure1', 'b_measure2', 'b_measure3', 'c_measure1', 'c_measure2', 'c_measure3',
                'd_measure1', 'd_measure2', 'd_measure3', 'e_measure1', 'e_measure2', 'e_measure3', 'f_measure1', 'f_measure2', 'f_measure3'
            ];
            foreach ($attributesToValidate as $attribute) {
                if (empty($this->$attribute) && $this->$attribute !== '0') {
                    $this->addError($attribute, 'Please fill out this field.');
                }
            }
        }
        if ($this->hasErrors()) {
            return false;
        }
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return true;
    }

    public static function addForm($id) {
        $model = new TestFormVisualpaint();
        $model->test_master_id = $id;
        $data = TestTemplate::find()->where(['formcode' => TestMaster::CODE_VISUALPAINT, 'active_sts' => 1])->one();
        if ($data) {
            if (empty(trim(strip_tags($data['proctest1']))) && empty(trim(strip_tags($data['proctest2']))) && empty(trim(strip_tags($data['proctest3'])))) {
                $model->template = null;
            } else {
                $templateData = [
                    'proctest1' => $data['proctest1'],
                    'proctest2' => $data['proctest2'],
                    'proctest3' => $data['proctest3'],
                ];
                $htmlContent = implode('|', $templateData);
                $testTemplate = new TestTemplate();
                $newhtml = $testTemplate->cleanHtmlContent($htmlContent);
                $model->template = preg_replace('/<(\w+)(\s*[^>]*)><\/\1>/', '', $newhtml);
            }
        } else {
            $model->template = null;
        }
        $model->rev_no = $data->rev_no ?? null;
        $model->doc_ref = $data->doc_ref ?? null;
        $model->treshold_a = TestFormVisualpaint::THRESHOLD_A;
        $model->status = RefTestStatus::STS_SETUP;
        return $model->save();
    }

    public function customProcedures($data, $templateAttribute, $inPDF) {
        if (!$templateAttribute) {
            $protest1 = $data['proctest1'];
            $protest2 = $this->setThreshold($data['proctest2'], TestFormVisualpaint::THRESHOLD_A, $inPDF);
        } else {
            $testTemplate = explode('|', $data->template);
            $protest1 = $testTemplate[0];
            $protest2 = $this->setThreshold($testTemplate[1], $data->treshold_a, $inPDF);
        }

        $templateData = [
            'proctest1' => $protest1,
            'proctest2' => $protest2,
        ];

        $procedures = implode('|', $templateData);

        return $procedures;
    }

    public function setThreshold($data, $threshold, $inPDF) {
        $replaceThreshold = $threshold;
        if ($inPDF) {
            $replaceThreshold = '<span style="color: #000; font-weight: normal;">' . $threshold . '</span>';
        }

        $value = str_replace('{{value}}', $replaceThreshold, $data);
        return $value;
    }

    public function copyOver($oldMasterId, $newMasterId) {
        $matrix = ['template', 'treshold_a'];

        $master = TestMaster::findOne($oldMasterId);
        $form = self::findOne($master->testFormVisualpaint->id);
        $newform = new TestFormVisualpaint();
        foreach ($matrix as $attribute) {
            $newform->$attribute = $form->$attribute;
        }
        $newform->test_master_id = $newMasterId;
        $newform->status = RefTestStatus::STS_SETUP;
        return $newform->save();
    }

}
