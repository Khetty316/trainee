<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\RefTestFormList;
use frontend\models\projectproduction\RefProjProdTaskErrors;

/**
 * This is the model class for table "test_detail_punchlist".
 *
 * @property int $id
 * @property int|null $form_punchlist_id
 * @property string|null $test_form_code
 * @property int|null $error_id
 * @property string|null $remark
 * @property string|null $rectify_date
 * @property string|null $verify_by
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormPunchlist $formPunchlist
 * @property RefTestFormList $testFormCode
 * @property ProductionElecTasksError $error
 */
class TestDetailPunchlist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_punchlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['remark', 'rectify_date', 'verify_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_punchlist_id', 'error_id', 'created_by', 'updated_by'], 'integer'],
            [['rectify_date', 'created_at', 'updated_at'], 'safe'],
            [['remark', 'verify_by'], 'string', 'max' => 255],
            [['form_punchlist_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormPunchlist::class, 'targetAttribute' => ['form_punchlist_id' => 'id']],
            [['test_form_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefTestFormList::class, 'targetAttribute' => ['test_form_code' => 'code']],
            [['error_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskErrors::class, 'targetAttribute' => ['error_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_punchlist_id' => 'Form Punchlist ID',
            'test_form_code' => 'Form',
            'error_id' => 'Error',
            'remark' => 'Remarks',
            'rectify_date' => 'Rectify Date',
            'verify_by' => 'Verify By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormPunchlist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormPunchlist() {
        return $this->hasOne(TestFormPunchlist::class, ['id' => 'form_punchlist_id']);
    }

    /**
     * Gets query for [[TestFormCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestFormCode() {
        return $this->hasOne(RefTestFormList::class, ['code' => 'test_form_code']);
    }

    /**
     * Gets query for [[Error]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getError() {
        return $this->hasOne(RefProjProdTaskErrors::class, ['id' => 'error_id']);
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

}
