<?php

namespace frontend\models\test;

use Yii;
use common\models\User;
use frontend\models\test\TestFormAttendance;
use frontend\models\test\TestFormInsuhipot;
use frontend\models\test\TestFormDimension;
use frontend\models\test\TestFormVisualpaint;
use frontend\models\test\TestFormComponent;
use frontend\models\test\TestFormAts;
use frontend\models\test\TestFormFunctionality;
use frontend\models\test\TestFormPunchlist;
use frontend\models\test\RefTestFormList;

/**
 * This is the model class for table "test_master".
 *
 * @property int $id
 * @property int|null $test_main_id
 * @property string|null $tc_ref
 * @property int|null $test_num
 * @property int|null $panel_qty
 * @property string|null $date
 * @property string|null $venue
 * @property string|null $detail
 * @property int|null $parent
 * @property int|null $status
 * @property string|null $tested_by
 * @property int|null $certified_by
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property TestMaster $parent0
 * @property RefTestStatus $status0
 * @property TestFormAts $testFormAts
 * @property TestFormAttendance $testFormAttendance
 * @property TestFormComponent $testFormComponent
 * @property TestFormDimension $testFormDimension
 * @property TestFormFunctionality $testFormFunctionality
 * @property TestFormInsuhipot $testFormInsuhipot
 * @property TestFormPunchlist $testFormPunchlist
 * @property TestFormVisualpaint $testFormVisualpaint
 * @property TestMain $testMain
 * @property TestMaster[] $testMasters
 * @property User $updatedBy
 */
class TestMaster extends \yii\db\ActiveRecord {

    const CODE_ATTENDANCE = 'att';
    const CODE_INSUHIPOT = 'hipot';
    const CODE_DIMENSION = 'dimen';
    const CODE_VISUALPAINT = 'visual';
    const CODE_COMPONENT = 'comp';
    const CODE_ATS = 'ats';
    const CODE_FUNCTIONALITY = 'func';
    const CODE_PUNCHLIST = 'punch';
    const TEMPLATE_ITP = 'itpdetail';
    const TEMPLATE_FAT = 'fatdetail';

    public $tester;
    public $testPlan;
    public $testType;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_main_id', 'tc_ref', 'test_num', 'panel_qty', 'date', 'venue', 'detail', 'parent', 'status', 'tested_by', 'certified_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['test_main_id', 'test_num', 'panel_qty', 'parent', 'status', 'certified_by', 'created_by', 'updated_by'], 'integer'],
            ['panel_qty', 'integer', 'message' => 'Panel quantity must be a number'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['detail'], 'string'],
            [['tested_by', 'tc_ref', 'venue'], 'string', 'max' => 255],
            [['test_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestMain::class, 'targetAttribute' => ['test_main_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => TestMaster::class, 'targetAttribute' => ['parent' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestStatus::class, 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'test_main_id' => 'Test Main ID',
            'tc_ref' => 'Tc Ref',
            'test_num' => 'Test Num',
            'panel_qty' => 'Panel Quantity',
            'date' => 'Date of Testing',
            'venue' => 'Venue',
            'detail' => 'Detail',
            'parent' => 'Parent',
            'status' => 'Status',
            'tested_by' => 'Tested By',
            'certified_by' => 'Certified By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Parent0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent0() {
        return $this->hasOne(TestMaster::class, ['id' => 'parent']);
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
     * Gets query for [[TestFormAts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormAts() {
        return $this->hasOne(TestFormAts::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormAttendance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormAttendance() {
        return $this->hasOne(TestFormAttendance::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormComponent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormComponent() {
        return $this->hasOne(TestFormComponent::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormDimension]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormDimension() {
        return $this->hasOne(TestFormDimension::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormFunctionality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormFunctionality() {
        return $this->hasOne(TestFormFunctionality::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormInsuhipot]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormInsuhipot() {
        return $this->hasOne(TestFormInsuhipot::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormPunchlist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormPunchlist() {
        return $this->hasOne(TestFormPunchlist::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestFormVisualpaint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormVisualpaint() {
        return $this->hasOne(TestFormVisualpaint::class, ['test_master_id' => 'id']);
    }

    /**
     * Gets query for [[TestMain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMain() {
        return $this->hasOne(TestMain::class, ['id' => 'test_main_id']);
    }

    /**
     * Gets query for [[TestMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMasters() {
        return $this->hasMany(TestMaster::class, ['parent' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
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

    public function getTestedBy() {
        return $this->hasOne(User::class, ['id' => 'tested_by']);
    }

    public static function getStatusBadge($status) {
        $constants = self::STS_ALL;

        foreach ($constants as $constant) {
            if ($constant['value'] == $status) {
                return $constant['badge'];
            }
        }

        return 'Unknown';
    }

    public function checkMasterStatus($id) {
        $master = TestMaster::findOne($id);
        $statuses = [];

        foreach (RefTestFormList::getClassname() as $form) {
            if (!empty($master->$form->status)) {
                $statuses[] = $master->$form->status;
            }
        }

        if (count(array_unique($statuses)) === 1) {
            $master->status = $statuses[0];
            $master->update();

            $message = '';
            switch ($master->status) {
                case RefTestStatus::STS_READY_FOR_TESTING:
                    $message = 'Test Set Up';
                    break;
                case RefTestStatus::STS_IN_TESTING:
                    $message = 'Test Starts';
                    break;
                case RefTestStatus::STS_COMPLETE:
                    $message = 'Test Completed';
                    break;
            }

            \common\models\myTools\FlashHandler::success($message);
            return true;
        } else {
            $master->status = min($statuses);
            $master->update();
        }

        return false;
    }

    /**
     * This function returns an array where either the forms are available or not in the test_master depending on the value of $selected
     * @param type $id
     * @param type $selected
     * @return type
     */
    private static function getForms($id, $selected = true) {
        $master = self::findOne($id);

        $forms = [
            ['code' => TestMaster::CODE_ATTENDANCE, 'exists' => $master->testFormAttendance],
            ['code' => TestMaster::CODE_INSUHIPOT, 'exists' => $master->testFormInsuhipot],
            ['code' => TestMaster::CODE_DIMENSION, 'exists' => $master->testFormDimension],
            ['code' => TestMaster::CODE_VISUALPAINT, 'exists' => $master->testFormVisualpaint],
            ['code' => TestMaster::CODE_COMPONENT, 'exists' => $master->testFormComponent],
            ['code' => TestMaster::CODE_ATS, 'exists' => $master->testFormAts],
            ['code' => TestMaster::CODE_FUNCTIONALITY, 'exists' => $master->testFormFunctionality],
            ['code' => TestMaster::CODE_PUNCHLIST, 'exists' => $master->testFormPunchlist],
        ];

        $codes = array_filter($forms, function ($form) use ($selected) {
            return $selected ? $form['exists'] : !$form['exists'];
        });
        $codes = array_column($codes, 'code');

        $choices = RefTestFormList::getDropDownCodeNameClass();
        $choices = array_filter($choices, function ($choice) use ($codes) {
            return in_array($choice['code'], $codes);
        });

        return $choices;
    }

    /**
     * Should be a clear name where it returns an array of unselected forms from a test_master 
     * @return [code]=>[ formname =>'string', formclass =>'string']
     */
    public static function getUnselectedForms($id) {
        return self::getForms($id, false);
    }

    /**
     * An opposite of getUnselectedForms function where this return what it selected/available in the test_master 
     * @return [code]=>[ formname =>'string', formclass =>'string']
     */
    public static function getSelectedForms($id) {
        return self::getForms($id, true);
    }

    public static function getDropdownUnselectedForms($id) {
        $forms = self::getUnselectedForms($id);

        $dropdown = [];
        foreach ($forms as $form) {
            $ref = RefTestFormList::find()->where(['formclass' => $form['formclass']])->one();
            if ($ref) {
                $dropdown[$ref->code] = $ref['formname'];
            }
        }
        return $dropdown;
    }

    public static function getDropdownSelectedForms($id) {
        $selectedForms = self::getSelectedForms($id);

        $dropdown = [];
        foreach ($selectedForms as $form) {
            $dropdown[$form['code']] = $form['formname'];
        }

        return $dropdown;
    }

}
