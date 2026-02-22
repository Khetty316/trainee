<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_detail_functionality".
 *
 * @property int $id
 * @property int|null $form_functionality_id
 * @property string|null $pot
 * @property string|null $pot_val
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormFunctionality $formFunctionality
 * @property RefTestPoints $pot0
 * @property TestItemFunctionality[] $testItemFunctionalities
 */
class TestDetailFunctionality extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_functionality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_functionality_id', 'pot', 'pot_val', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_functionality_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['pot', 'pot_val'], 'string', 'max' => 255],
            [['form_functionality_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormFunctionality::class, 'targetAttribute' => ['form_functionality_id' => 'id']],
            [['pot'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestPoints::class, 'targetAttribute' => ['pot' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_functionality_id' => 'Form Functionality ID',
            'pot' => 'Point of Test',
            'pot_val' => 'Point of Test No.',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormFunctionality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormFunctionality() {
        return $this->hasOne(TestFormFunctionality::class, ['id' => 'form_functionality_id']);
    }

    /**
     * Gets query for [[Pot0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPot0() {
        return $this->hasOne(RefTestPoints::class, ['code' => 'pot']);
    }

    /**
     * Gets query for [[TestItemFunctionalities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestItemFunctionalities() {
        return $this->hasMany(TestItemFunctionality::class, ['detail_functionality_id' => 'id']);
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

    public function copyOver($oldFormId, $newFormId) {
        $matrix = ['pot', 'pot_val'];

        $form = TestFormFunctionality::findOne($oldFormId);
        $oldDetails = $form->testDetailFunctionalities;
        $allSaved = true;

        foreach ($oldDetails as $detail) {
            $newDetail = new TestDetailFunctionality();
            $newDetail->status = RefTestStatus::STS_SETUP;
            foreach ($matrix as $attribute) {
                $newDetail->$attribute = $detail->$attribute;
            }
            $newDetail->form_functionality_id = $newFormId;
            if (!$newDetail->save()) {
                $allSaved = false;
            } else {
                $item = new TestItemFunctionality();
                $allSaved = $item->copyOver($detail->id, $newDetail->id);
            }
        }

        return $allSaved;
    }

}
