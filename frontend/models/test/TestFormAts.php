<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_form_ats".
 *
 * @property int $id
 * @property int|null $test_master_id
 * @property string|null $template
 * @property int|null $status
 * @property string|null $head_acot_1
 * @property string|null $head_acot_2
 * @property string|null $head_acot_3
 * @property string|null $head_acot_4
 * @property string|null $head_acot_5
 * @property string|null $head_acot_6
 * @property string|null $head_acot_7
 * @property string|null $head_acot_8
 * @property string|null $head_acot_9
 * @property string|null $head_acot_10
 * @property string|null $res_acot
 * @property string|null $res_mcot
 * @property string|null $head_cbvc_1
 * @property string|null $head_cbvc_2
 * @property string|null $head_cbvc_3
 * @property string|null $head_cbvc_4
 * @property string|null $head_cbvc_5
 * @property string|null $head_cbvc_6
 * @property string|null $head_cbvc_7
 * @property string|null $head_cbvc_8
 * @property string|null $head_cbvc_9
 * @property string|null $head_cbvc_10
 * @property string|null $head_cbvc_11
 * @property string|null $head_cbvc_12
 * @property string|null $head_cbvc_13
 * @property string|null $head_cbvc_14
 * @property string|null $head_cbvc_15
 * @property string|null $res_cbvc
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefTestStatus $status0
 * @property TestDetailAts[] $testDetailAts
 * @property TestMaster $testMaster
 */
class TestFormAts extends \yii\db\ActiveRecord {

    public $submitSts;

    const DEFAULT_ACOT = ['Incoming Breaker No.1', 'Coupler', 'Incoming Breaker No.2'];
    const DEFAULT_MCOT = ['Incoming Breaker No.1', 'Coupler', 'Incoming Breaker No.2'];
    const DEFAULT_CBVC_BREAKER = ['Incoming Breaker No.1', 'Coupler', 'Incoming Breaker No.2'];
    const DEFAULT_CBVC_BUSBAR = ['Busbar Section A', 'Busbar Section B'];
    const HEAD_ACOT = 'head_acot_';
    const VAL_ACOT = 'val_acot_';
    const HEAD_CBVC = 'head_cbvc_';
    const VAL_CBVC = 'val_cbvc_';
    const RES_ACOT = 'res_acot';
    const RES_MCOT = 'res_mcot';
    const RES_CBVC = 'res_cbvc';
    const RESULT_PASS = 1;
    const RESULT_FAIL = 0;
    const FORM_TYPE_ACOT = 'acot';
    const FORM_TYPE_MCOT = 'mcot';
    const FORM_TYPE_CBVC = 'cbvc';
    const FORM_ALL = [self::FORM_TYPE_ACOT, self::FORM_TYPE_MCOT, self::FORM_TYPE_CBVC];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_form_ats';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'template', 'status', 'head_acot_1', 'head_acot_2', 'head_acot_3', 'head_acot_4', 'head_acot_5', 'head_acot_6', 'head_acot_7', 'head_acot_8', 'head_acot_9', 'head_acot_10', 'res_acot', 'res_mcot', 'head_cbvc_1', 'head_cbvc_2', 'head_cbvc_3', 'head_cbvc_4', 'head_cbvc_5', 'head_cbvc_6', 'head_cbvc_7', 'head_cbvc_8', 'head_cbvc_9', 'head_cbvc_10', 'head_cbvc_11', 'head_cbvc_12', 'head_cbvc_13', 'head_cbvc_14', 'head_cbvc_15', 'res_cbvc', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_master_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['template'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['head_acot_1', 'head_acot_2', 'head_acot_3', 'head_acot_4', 'head_acot_5', 'head_acot_6', 'head_acot_7', 'head_acot_8', 'head_acot_9', 'head_acot_10', 'res_acot', 'res_mcot', 'head_cbvc_1', 'head_cbvc_2', 'head_cbvc_3', 'head_cbvc_4', 'head_cbvc_5', 'head_cbvc_6', 'head_cbvc_7', 'head_cbvc_8', 'head_cbvc_9', 'head_cbvc_10', 'head_cbvc_11', 'head_cbvc_12', 'head_cbvc_13', 'head_cbvc_14', 'head_cbvc_15', 'res_cbvc'], 'string', 'max' => 255],
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
            'template' => 'Template',
            'status' => 'Status',
            'head_acot_1' => 'Head Acot 1',
            'head_acot_2' => 'Head Acot 2',
            'head_acot_3' => 'Head Acot 3',
            'head_acot_4' => 'Head Acot 4',
            'head_acot_5' => 'Head Acot 5',
            'head_acot_6' => 'Head Acot 6',
            'head_acot_7' => 'Head Acot 7',
            'head_acot_8' => 'Head Acot 8',
            'head_acot_9' => 'Head Acot 9',
            'head_acot_10' => 'Head Acot 10',
            'res_acot' => 'Res Acot',
            'res_mcot' => 'Res Mcot',
            'head_cbvc_1' => 'Head Cbvc 1',
            'head_cbvc_2' => 'Head Cbvc 2',
            'head_cbvc_3' => 'Head Cbvc 3',
            'head_cbvc_4' => 'Head Cbvc 4',
            'head_cbvc_5' => 'Head Cbvc 5',
            'head_cbvc_6' => 'Head Cbvc 6',
            'head_cbvc_7' => 'Head Cbvc 7',
            'head_cbvc_8' => 'Head Cbvc 8',
            'head_cbvc_9' => 'Head Cbvc 9',
            'head_cbvc_10' => 'Head Cbvc 10',
            'head_cbvc_11' => 'Head Cbvc 11',
            'head_cbvc_12' => 'Head Cbvc 12',
            'head_cbvc_13' => 'Head Cbvc 13',
            'head_cbvc_14' => 'Head Cbvc 14',
            'head_cbvc_15' => 'Head Cbvc 15',
            'res_cbvc' => 'Res Cbvc',
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
     * Gets query for [[TestDetailAts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailAts() {
        return $this->hasMany(TestDetailAts::class, ['form_ats_id' => 'id']);
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
        if ($this->isNewRecord) {
            $this->initializeForm();
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

    public static function addForm($id) {
        $model = new TestFormAts();
        $model->test_master_id = $id;
        $model->status = RefTestStatus::STS_SETUP;
        return $model->save();
    }

    public function initializeForm() {
        $this->saveHeader(self::DEFAULT_ACOT, self::HEAD_ACOT);
        $this->saveHeader(self::DEFAULT_CBVC_BREAKER, self::HEAD_CBVC, TestDetailAts::TYPE_BREAKER);
        $this->saveHeader(self::DEFAULT_CBVC_BUSBAR, self::HEAD_CBVC, TestDetailAts::TYPE_BUSBAR);
        $this->res_acot = 'Result';
        $this->res_mcot = 'Result';
        $this->res_cbvc = 'Result';
    }

    public function saveHeader($textArray, $formCode, $type = null) {
        if ($type == TestDetailAts::TYPE_BREAKER || $type == null) {
            for ($i = 0; $i < 11; $i++) {
                $attributeName = $formCode . ($i + 1);
                if (in_array($attributeName, $this->attributes())) {
                    $this->$attributeName = empty($textArray[$i]) ? null : $textArray[$i];
                }
            }
        } else if ($type == TestDetailAts::TYPE_BUSBAR) {
            for ($i = 11; $i < 16; $i++) {
                $attributeName = $formCode . ($i);
                if (in_array($attributeName, $this->attributes())) {
                    $this->$attributeName = empty($textArray[$i - 11]) ? null : $textArray[$i - 11];
                }
            }
        }
    }

    public function saveColumnValue($textArray, $formCode) {
        for ($i = 1; $i < 11; $i++) {
            $attributeName = $formCode . ($i);
            if ($textArray == $attributeName) {
                $this->$attributeName = $textArray[$i];
                $this->update(false);
            }
        }
        return;
    }

    public function addHeaderAcot() {
        for ($i = 1; $i < 11; $i++) {
            $header = self::HEAD_ACOT . $i;
            if (empty($this->$header)) {
                $this->$header = 'Breaker';
                return $this->save();
            }
        }
    }

    public function addHeaderCbvc($type) {
        if ($type == TestDetailAts::TYPE_BREAKER) {
            for ($i = 1; $i < 11; $i++) {
                $header = self::HEAD_CBVC . $i;
                if (empty($this->$header)) {
                    $this->$header = 'Breaker';
                    return $this->save();
                }
            }
            \common\models\myTools\FlashHandler::success('Breaker limited to 10. Please contact IT department.');
            return;
        } else if ($type == TestDetailAts::TYPE_BUSBAR) {
            for ($i = 11; $i < 16; $i++) {
                $header = self::HEAD_CBVC . $i;
                if (empty($this->$header)) {
                    $this->$header = 'Busbar';
                    return $this->save();
                }
            }
            \common\models\myTools\FlashHandler::success('Busbar limited to 5. Please contact IT department.');
            return;
        }
    }

    public function copyOver($oldMasterId, $newMasterId) {
        $matrix = ['template', 'head_acot_1', 'head_acot_2', 'head_acot_3', 'head_acot_4', 'head_acot_5', 'head_acot_6', 'head_acot_7', 'head_acot_8',
            'head_acot_9', 'head_acot_10', 'head_cbvc_1', 'head_cbvc_2', 'head_cbvc_3', 'head_cbvc_4', 'head_cbvc_5', 'head_cbvc_6', 'head_cbvc_7',
            'head_cbvc_8', 'head_cbvc_9', 'head_cbvc_10', 'head_cbvc_11', 'head_cbvc_12', 'head_cbvc_13', 'head_cbvc_14', 'head_cbvc_15'];

        $master = TestMaster::findOne($oldMasterId);
        $form = self::findOne($master->testFormAts->id);
        $newform = new TestFormAts();
        foreach ($matrix as $attribute) {
            $newform->$attribute = $form->$attribute;
        }
        $newform->test_master_id = $newMasterId;
        $newform->status = RefTestStatus::STS_SETUP;
        if ($newform->save()) {
            $detail = new TestDetailAts();
            return $detail->copyOver($master->testFormAts->id, $newform->id);
        }
    }

}
