<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\TestFormAts;

/**
 * This is the model class for table "test_detail_ats".
 *
 * @property int $id
 * @property int|null $form_ats_id
 * @property string|null $form_type
 * @property string|null $mode
 * @property int|null $val_acot_1
 * @property int|null $val_acot_2
 * @property int|null $val_acot_3
 * @property int|null $val_acot_4
 * @property int|null $val_acot_5
 * @property int|null $val_acot_6
 * @property int|null $val_acot_7
 * @property int|null $val_acot_8
 * @property int|null $val_acot_9
 * @property int|null $val_acot_10
 * @property int|null $res_acot
 * @property int|null $res_mcot
 * @property int|null $val_cbvc_1
 * @property int|null $val_cbvc_2
 * @property int|null $val_cbvc_3
 * @property int|null $val_cbvc_4
 * @property int|null $val_cbvc_5
 * @property int|null $val_cbvc_6
 * @property int|null $val_cbvc_7
 * @property int|null $val_cbvc_8
 * @property int|null $val_cbvc_9
 * @property int|null $val_cbvc_10
 * @property int|null $val_cbvc_11
 * @property int|null $val_cbvc_12
 * @property int|null $val_cbvc_13
 * @property int|null $val_cbvc_14
 * @property int|null $val_cbvc_15
 * @property int|null $res_cbvc
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormAts $formAts
 */
class TestDetailAts extends \yii\db\ActiveRecord {

    const TYPE_BREAKER = 'breaker';
    const TYPE_BUSBAR = 'busbar';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_ats';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_ats_id', 'form_type', 'mode', 'val_acot_1', 'val_acot_2', 'val_acot_3', 'val_acot_4', 'val_acot_5', 'val_acot_6', 'val_acot_7', 'val_acot_8', 'val_acot_9', 'val_acot_10', 'res_acot', 'res_mcot', 'val_cbvc_1', 'val_cbvc_2', 'val_cbvc_3', 'val_cbvc_4', 'val_cbvc_5', 'val_cbvc_6', 'val_cbvc_7', 'val_cbvc_8', 'val_cbvc_9', 'val_cbvc_10', 'val_cbvc_11', 'val_cbvc_12', 'val_cbvc_13', 'val_cbvc_14', 'val_cbvc_15', 'res_cbvc', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_ats_id', 'val_acot_1', 'val_acot_2', 'val_acot_3', 'val_acot_4', 'val_acot_5', 'val_acot_6', 'val_acot_7', 'val_acot_8', 'val_acot_9', 'val_acot_10', 'res_acot', 'res_mcot', 'val_cbvc_1', 'val_cbvc_2', 'val_cbvc_3', 'val_cbvc_4', 'val_cbvc_5', 'val_cbvc_6', 'val_cbvc_7', 'val_cbvc_8', 'val_cbvc_9', 'val_cbvc_10', 'val_cbvc_11', 'val_cbvc_12', 'val_cbvc_13', 'val_cbvc_14', 'val_cbvc_15', 'res_cbvc', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['form_type', 'mode'], 'string', 'max' => 255],
            [['form_ats_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormAts::class, 'targetAttribute' => ['form_ats_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_ats_id' => 'Form Ats ID',
            'form_type' => 'Form Type',
            'mode' => 'Mode',
            'val_acot_1' => 'Val Acot 1',
            'val_acot_2' => 'Val Acot 2',
            'val_acot_3' => 'Val Acot 3',
            'val_acot_4' => 'Val Acot 4',
            'val_acot_5' => 'Val Acot 5',
            'val_acot_6' => 'Val Acot 6',
            'val_acot_7' => 'Val Acot 7',
            'val_acot_8' => 'Val Acot 8',
            'val_acot_9' => 'Val Acot 9',
            'val_acot_10' => 'Val Acot 10',
            'res_acot' => 'Res Acot',
            'res_mcot' => 'Res Mcot',
            'val_cbvc_1' => 'Val Cbvc 1',
            'val_cbvc_2' => 'Val Cbvc 2',
            'val_cbvc_3' => 'Val Cbvc 3',
            'val_cbvc_4' => 'Val Cbvc 4',
            'val_cbvc_5' => 'Val Cbvc 5',
            'val_cbvc_6' => 'Val Cbvc 6',
            'val_cbvc_7' => 'Val Cbvc 7',
            'val_cbvc_8' => 'Val Cbvc 8',
            'val_cbvc_9' => 'Val Cbvc 9',
            'val_cbvc_10' => 'Val Cbvc 10',
            'val_cbvc_11' => 'Val Cbvc 11',
            'val_cbvc_12' => 'Val Cbvc 12',
            'val_cbvc_13' => 'Val Cbvc 13',
            'val_cbvc_14' => 'Val Cbvc 14',
            'val_cbvc_15' => 'Val Cbvc 15',
            'res_cbvc' => 'Res Cbvc',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormAts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormAts() {
        return $this->hasOne(TestFormAts::class, ['id' => 'form_ats_id']);
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

    public function saveRowValue($textArray) {
        foreach ($textArray as $attribute => $value) {
            $this->$attribute = $value;
        }
        $this->save();
    }

    public function copyOver($oldFormId, $newFormId) {
        $matrix = ['form_type', 'mode', 'val_acot_1', 'val_acot_2', 'val_acot_3', 'val_acot_4', 'val_acot_5', 'val_acot_6', 'val_acot_7', 'val_acot_8',
            'val_acot_9', 'val_acot_10', 'res_acot', 'res_mcot', 'val_cbvc_1', 'val_cbvc_2', 'val_cbvc_3', 'val_cbvc_4', 'val_cbvc_5', 'val_cbvc_6',
            'val_cbvc_7', 'val_cbvc_8', 'val_cbvc_9', 'val_cbvc_10', 'val_cbvc_11', 'val_cbvc_12', 'val_cbvc_13', 'val_cbvc_14', 'val_cbvc_15'];

        $form = TestFormAts::findOne($oldFormId);
        $oldDetails = $form->testDetailAts;
        $allSaved = true;

        foreach ($oldDetails as $detail) {
            $newDetail = new TestDetailAts();
            foreach ($matrix as $attribute) {
                if ($attribute != 'form_type' && $attribute != 'mode') {
                    $newDetail->$attribute = 0;
                } else {
                    $newDetail->$attribute = $detail->$attribute;
                }
            }
            $newDetail->form_ats_id = $newFormId;
            if (!$newDetail->save()) {
                $allSaved = false;
            }
        }

        return $allSaved;
    }

}
