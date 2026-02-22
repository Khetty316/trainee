<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;

/**
 * This is the model class for table "test_form_insuhipot".
 *
 * @property int $id
 * @property int|null $test_master_id
 * @property string|null $rev_no
 * @property string|null $doc_ref
 * @property string|null $template
 * @property string|null $remark
 * @property int|null $status
 * @property float|null $treshold_a
 * @property float|null $treshold_b
 * @property float|null $re1
 * @property float|null $re2
 * @property float|null $ye1
 * @property float|null $ye2
 * @property float|null $be1
 * @property float|null $be2
 * @property float|null $ne1
 * @property float|null $ne2
 * @property float|null $rn1
 * @property float|null $rn2
 * @property float|null $yn1
 * @property float|null $yn2
 * @property float|null $bn1
 * @property float|null $bn2
 * @property float|null $ry1
 * @property float|null $ry2
 * @property float|null $yb1
 * @property float|null $yb2
 * @property float|null $br1
 * @property float|null $br2
 * @property float|null $r_start
 * @property float|null $r_end
 * @property string|null $r_time
 * @property float|null $y_start
 * @property float|null $y_end
 * @property string|null $y_time
 * @property float|null $b_start
 * @property float|null $b_end
 * @property string|null $b_time
 * @property int|null $res_re
 * @property int|null $res_ye
 * @property int|null $res_be
 * @property int|null $res_ne
 * @property int|null $res_rn
 * @property int|null $res_yn
 * @property int|null $res_bn
 * @property int|null $res_ry
 * @property int|null $res_yb
 * @property int|null $res_br
 * @property int|null $res_r
 * @property int|null $res_y
 * @property int|null $res_b
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestMaster $testMaster
 * @property RefTestStatus $status0
 */
class TestFormInsuhipot extends \yii\db\ActiveRecord {

    const THRESHOLD_A = 1.0;
    const THRESHOLD_B = 10;
    const THRESHOLD_A_UNIT = 'MOhm';
    const THRESHOLD_B_UNIT = 'mA';
    const RESULT_FAIL = ['value' => 0];
    const RESULT_PASS = ['value' => 1];

    public $result_text;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_insuhipot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'template', 'remark', 'status', 'treshold_a', 'treshold_b', 're1', 're2', 'ye1', 'ye2', 'be1', 'be2', 'ne1', 'ne2', 'rn1', 'rn2', 'yn1', 'yn2', 'bn1', 'bn2', 'ry1', 'ry2', 'yb1', 'yb2', 'br1', 'br2', 'r_start', 'r_end', 'r_time', 'y_start', 'y_end', 'y_time', 'b_start', 'b_end', 'b_time', 'res_re', 'res_ye', 'res_be', 'res_ne', 'res_rn', 'res_yn', 'res_bn', 'res_ry', 'res_yb', 'res_br', 'res_r', 'res_y', 'res_b', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_master_id', 'status', 'res_re', 'res_ye', 'res_be', 'res_ne', 'res_rn', 'res_yn', 'res_bn', 'res_ry', 'res_yb', 'res_br', 'res_r', 'res_y', 'res_b', 'created_by', 'updated_by'], 'integer'],
            [['template'], 'string'],
            [['treshold_a', 'treshold_b', 're1', 're2', 'ye1', 'ye2', 'be1', 'be2', 'ne1', 'ne2', 'rn1', 'rn2', 'yn1', 'yn2', 'bn1', 'bn2', 'ry1', 'ry2', 'yb1', 'yb2', 'br1', 'br2', 'r_start', 'r_end', 'y_start', 'y_end', 'b_start', 'b_end'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['rev_no', 'doc_ref', 'remark'], 'string', 'max' => 255],
            [['r_time', 'y_time', 'b_time'], 'string', 'max' => 10],
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
            'remark' => 'Remark',
            'status' => 'Status',
            'treshold_a' => 'Treshold',
            'treshold_b' => 'Treshold',
            're1' => 'Re1',
            're2' => 'Re2',
            'ye1' => 'Ye1',
            'ye2' => 'Ye2',
            'be1' => 'Be1',
            'be2' => 'Be2',
            'ne1' => 'Ne1',
            'ne2' => 'Ne2',
            'rn1' => 'Rn1',
            'rn2' => 'Rn2',
            'yn1' => 'Yn1',
            'yn2' => 'Yn2',
            'bn1' => 'Bn1',
            'bn2' => 'Bn2',
            'ry1' => 'Ry1',
            'ry2' => 'Ry2',
            'yb1' => 'Yb1',
            'yb2' => 'Yb2',
            'br1' => 'Br1',
            'br2' => 'Br2',
            'r_start' => 'R Start',
            'r_end' => 'R End',
            'r_time' => 'R Time',
            'y_start' => 'Y Start',
            'y_end' => 'Y End',
            'y_time' => 'Y Time',
            'b_start' => 'B Start',
            'b_end' => 'B End',
            'b_time' => 'B Time',
            'res_re' => 'Res Re',
            'res_ye' => 'Res Ye',
            'res_be' => 'Res Be',
            'res_ne' => 'Res Ne',
            'res_rn' => 'Res Rn',
            'res_yn' => 'Res Yn',
            'res_bn' => 'Res Bn',
            'res_ry' => 'Res Ry',
            'res_yb' => 'Res Yb',
            'res_br' => 'Res Br',
            'res_r' => 'Res R',
            'res_y' => 'Res Y',
            'res_b' => 'Res B',
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
            $attributesToValidate = ['re1', 're2', 'ye1', 'ye2', 'be1', 'be2', 'ne1', 'ne2', 'rn1', 'rn2', 'yn1', 'yn2', 'bn1', 'bn2', 'ry1',
                'ry2', 'yb1', 'yb2', 'br1', 'br2', 'r_start', 'r_end', 'r_time', 'y_start', 'y_end', 'y_time', 'b_start', 'b_end', 'b_time'];
            foreach ($attributesToValidate as $attribute) {
                if (empty($this->$attribute)) {
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
        $model = new TestFormInsuhipot();
        $model->test_master_id = $id;
        $data = TestTemplate::find()->where(['formcode' => TestMaster::CODE_INSUHIPOT, 'active_sts' => 1])->one();
        if ($data) {
            if (empty(trim(strip_tags($data['proctest1']))) && empty(trim(strip_tags($data['proctest2']))) && empty(trim(strip_tags($data['proctest1'])))) {
                $model->template = null;
            } else {
//                $model->treshold_a = TestFormInsuhipot::THRESHOLD_A;
//                $model->treshold_b = TestFormInsuhipot::THRESHOLD_B;

                $templateData = [
                    'proctest1' => $data['proctest1'],
                    'proctest2' => $data['proctest2'],
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
        $model->treshold_a = TestFormInsuhipot::THRESHOLD_A;
        $model->treshold_b = TestFormInsuhipot::THRESHOLD_B;
        $model->status = RefTestStatus::STS_SETUP;
        return $model->save();
    }

    public function customProcedures($data, $templateAttribute, $inPDF) {
        if (!$templateAttribute) {
            $protest1 = $this->setThreshold($data['proctest1'], TestFormInsuhipot::THRESHOLD_A, $inPDF);
            $protest2 = $this->setThreshold($data['proctest2'], TestFormInsuhipot::THRESHOLD_B, $inPDF);
        } else {
            $testTemplate = explode('|', $data->template);
            $protest1 = $this->setThreshold($testTemplate[0], $data->treshold_a, $inPDF);
            $protest2 = $this->setThreshold($testTemplate[1], $data->treshold_b, $inPDF);
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
        $matrix = ['template', 'treshold_a', 'treshold_b'];

        $master = TestMaster::findOne($oldMasterId);
        $form = self::findOne($master->testFormInsuhipot->id);
        $newform = new TestFormInsuhipot();
        foreach ($matrix as $attribute) {
            $newform->$attribute = $form->$attribute;
        }
        $newform->test_master_id = $newMasterId;
        $newform->status = RefTestStatus::STS_SETUP;
        return $newform->save();
    }

}
