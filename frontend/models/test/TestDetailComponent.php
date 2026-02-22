<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\RefTestCompType;
use frontend\models\test\RefTestPoints;
use frontend\models\test\TestItemCompOther;

/**
 * This is the model class for table "test_detail_component".
 *
 * @property int $id
 * @property int $form_component_id
 * @property string|null $comp_type
 * @property string|null $comp_name
 * @property string|null $pou
 * @property string|null $pou_val
 * @property string|null $function_type
 * @property string|null $make
 * @property string|null $type
 * @property string|null $serial_num
 * @property string|null $prot_mode
 * @property string|null $particular_fn
 * @property string|null $amps
 * @property string|null $breakcap
 * @property string|null $accessory
 * @property string|null $acc_class
 * @property string|null $ratio_a
 * @property string|null $ratio_b
 * @property string|null $burden
 * @property string|null $voltage
 * @property string|null $setting
 * @property string|null $tms
 * @property string|null $nominal_dc
 * @property string|null $dimension_a
 * @property string|null $dimension_b
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefTestCompType $compType
 * @property TestFormComponent $formComponent
 * @property RefTestCompFunc $functionType
 * @property RefTestPoints $pou0
 * @property TestItemCompOther[] $testItemCompOthers
 */
class TestDetailComponent extends \yii\db\ActiveRecord {

    const ATTRIBUTE_COMPTYPE = 'comp_type';
    const ATTRIBUTE_COMPNAME = 'comp_name';
    const ATTRIBUTE_POU = 'pou';
    const ATTRIBUTE_POUVAL = 'pou_val';
    const ATTRIBUTE_FUNCTIONTYPE = 'function_type';
    const ATTRIBUTE_MAKE = 'make';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_SERIALNUM = 'serial_num';
    const ATTRIBUTE_PROTECTIONMODE = 'prot_mode';
    const ATTRIBUTE_PARTICULARFUNCTION = 'particular_fn';
    const ATTRIBUTE_AMPS = 'amps';
    const ATTRIBUTE_BREAKCAP = 'breakcap';
    const ATTRIBUTE_ACCESSORY = 'accessory';
    const ATTRIBUTE_ACCCLASS = 'acc_class';
    const ATTRIBUTE_RATIOA = 'ratio_a';
    const ATTRIBUTE_RATIOB = 'ratio_b';
    const ATTRIBUTE_BURDEN = 'burden';
    const ATTRIBUTE_VOLTAGE = 'voltage';
    const ATTRIBUTE_SETTING = 'setting';
    const ATTRIBUTE_TMS = 'tms';
    const ATTRIBUTE_NOMINALDC = 'nominal_dc';
    const ATTRIBUTE_DIMENSIONA = 'dimension_a';
    const ATTRIBUTE_DIMENSIONB = 'dimension_b';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_component';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['comp_type', 'comp_name', 'pou', 'pou_val', 'function_type', 'make', 'type', 'serial_num', 'amps', 'breakcap', 'accessory', 'acc_class', 'ratio_a', 'ratio_b', 'burden', 'voltage', 'setting', 'tms', 'dimension_a', 'dimension_b', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_component_id'], 'required'],
            [['form_component_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['comp_type', 'comp_name', 'pou', 'pou_val', 'function_type', 'make', 'type', 'serial_num', 'prot_mode', 'particular_fn', 'amps', 'breakcap', 'accessory', 'acc_class', 'ratio_a', 'ratio_b', 'burden', 'voltage', 'setting', 'tms', 'nominal_dc', 'dimension_a', 'dimension_b'], 'string', 'max' => 255],
            [['form_component_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormComponent::class, 'targetAttribute' => ['form_component_id' => 'id']],
            [['comp_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestCompType::class, 'targetAttribute' => ['comp_type' => 'code']],
            [['pou'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestPoints::class, 'targetAttribute' => ['pou' => 'code']],
            [['function_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestCompFunc::class, 'targetAttribute' => ['function_type' => 'code']],
            ['comp_type', 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_component_id' => 'Form Component ID',
            'comp_type' => 'Component Type',
            'comp_name' => 'Component Name',
            'pou' => 'Point Of Use',
            'pou_val' => 'Point Of Use Detail',
            'function_type' => 'Function',
            'make' => 'Make',
            'type' => 'Type',
            'serial_num' => 'Serial Number',
            'prot_mode' => 'Protection Mode',
            'particular_fn' => 'Particular Function',
            'amps' => 'Rated Amps (A)',
            'breakcap' => 'Breaking Cap (kA)',
            'accessory' => 'Accessory',
            'acc_class' => 'Accuracy Class',
            'ratio_a' => 'Ratio A',
            'ratio_b' => 'Ratio B',
            'burden' => 'Burden (va)',
            'voltage' => 'Voltage',
            'setting' => 'Setting',
            'tms' => 'Tms',
            'nominal_dc' => 'Nominal Discharge Current (kA)',
            'dimension_a' => 'Width (mm)',
            'dimension_b' => 'Thickness (mm)',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormComponent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormComponent() {
        return $this->hasOne(TestFormComponent::className(), ['id' => 'form_component_id']);
    }

    /**
     * Gets query for [[CompType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompType() {
        return $this->hasOne(RefTestCompType::className(), ['code' => 'comp_type']);
    }

    /**
     * Gets query for [[Pou0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPou0() {
        return $this->hasOne(RefTestPoints::className(), ['code' => 'pou']);
    }

    /**
     * Gets query for [[FunctionType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFunctionType() {
        return $this->hasOne(RefTestCompFunc::className(), ['code' => 'function_type']);
    }

    /**
     * Gets query for [[TestItemCompOthers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestItemCompOthers() {
        return $this->hasMany(TestItemCompOther::className(), ['detail_component_id' => 'id']);
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

    public static function getVisibilitySettingsForType($type) {
        switch ($type) {
            case RefTestCompType::TYPE_BREAKER:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_SERIALNUM,
                    self::ATTRIBUTE_AMPS,
                    self::ATTRIBUTE_BREAKCAP,
                    self::ATTRIBUTE_ACCESSORY
                ];
            case RefTestCompType::TYPE_MCT:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_ACCCLASS,
                    self::ATTRIBUTE_RATIOA,
                    self::ATTRIBUTE_RATIOB,
                    self::ATTRIBUTE_BURDEN,
                    self::ATTRIBUTE_VOLTAGE,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_PCT:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_ACCCLASS,
                    self::ATTRIBUTE_RATIOA,
                    self::ATTRIBUTE_RATIOB,
                    self::ATTRIBUTE_BURDEN,
                    self::ATTRIBUTE_VOLTAGE,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_METER:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_PARTICULARFUNCTION,
                    self::ATTRIBUTE_ACCCLASS,
                    self::ATTRIBUTE_RATIOA,
                    self::ATTRIBUTE_RATIOB,
                    self::ATTRIBUTE_BURDEN,
                    self::ATTRIBUTE_VOLTAGE,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_POWER:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_ACCCLASS,
                    self::ATTRIBUTE_RATIOA,
                    self::ATTRIBUTE_RATIOB,
                    self::ATTRIBUTE_BURDEN,
                    self::ATTRIBUTE_VOLTAGE,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_PRORELAY:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_FUNCTIONTYPE,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_SETTING,
                    self::ATTRIBUTE_TMS,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_SURGE:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_MAKE,
                    self::ATTRIBUTE_TYPE,
                    self::ATTRIBUTE_PROTECTIONMODE,
                    self::ATTRIBUTE_ACCCLASS,
                    self::ATTRIBUTE_NOMINALDC,
                    self::ATTRIBUTE_SERIALNUM
                ];
            case RefTestCompType::TYPE_BUSBAR:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_DIMENSIONA,
                    self::ATTRIBUTE_DIMENSIONB
                ];
            case RefTestCompType::TYPE_OTHER:
                return [
                    self::ATTRIBUTE_POU,
                    self::ATTRIBUTE_POUVAL,
                    self::ATTRIBUTE_COMPNAME,
                ];
        }
    }

    public function copyOver($oldFormId, $newFormId) {
        $matrix = ['comp_type', 'comp_name', 'pou', 'pou_val', 'function_type', 'make', 'type', 'serial_num', 'amps', 'breakcap',
            'accessory', 'acc_class', 'ratio_a', 'ratio_b', 'burden', 'voltage', 'setting', 'tms', 'dimension_a', 'dimension_b'];

        $form = TestFormComponent::findOne($oldFormId);
        $oldDetails = $form->testDetailComponents;
        $allSaved = true;

        foreach ($oldDetails as $detail) {
            $newDetail = new TestDetailComponent();
            foreach ($matrix as $attribute) {
                $newDetail->$attribute = $detail->$attribute;
            }
            $newDetail->form_component_id = $newFormId;
            if (!$newDetail->save()) {
                $allSaved = false;
            } else {
                $item = new TestItemCompOther();
                $allSaved = $item->copyOver($detail->id, $newDetail->id);
            }
        }

        return $allSaved;
    }

    public static function getAutoCompleteListAttr($attribute, $compType) {
        $list = self::find()
                ->select(["$attribute as value", "$attribute as label"])
                ->groupBy('make')
                ->where(['comp_type' => $compType])
                ->andWhere(['not', [$attribute => null]])
                ->asArray()
                ->all();
        return $list;
    }

}
