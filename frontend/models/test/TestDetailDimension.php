<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_detail_dimension".
 *
 * @property int $id
 * @property int|null $form_dimension_id
 * @property string|null $panel_name
 * @property int|null $drawing_h
 * @property int|null $drawing_w
 * @property int|null $drawing_d
 * @property int|null $built_h
 * @property int|null $built_w
 * @property int|null $built_d
 * @property float|null $error_h
 * @property float|null $error_w
 * @property float|null $error_d
 * @property int|null $res_h
 * @property int|null $res_w
 * @property int|null $res_d
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormDimension $formDimension
 */
class TestDetailDimension extends \yii\db\ActiveRecord {

    public $result_text;
    public $dimensionPanel;
    public $dimensionResHText;
    public $dimensionResWText;
    public $dimensionResDText;
    public $toDelete;
    public $status;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_dimension';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_dimension_id', 'drawing_h', 'drawing_w', 'drawing_d', 'built_h', 'built_w', 'built_d', 'res_h', 'res_w', 'res_d', 'created_by', 'updated_by'], 'integer'],
            [['error_h', 'error_w', 'error_d'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['panel_name'], 'string', 'max' => 255],
            [['form_dimension_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormDimension::className(), 'targetAttribute' => ['form_dimension_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_dimension_id' => 'Form Dimension ID',
            'panel_name' => 'Panel Name',
            'drawing_h' => 'Drawing H',
            'drawing_w' => 'Drawing W',
            'drawing_d' => 'Drawing D',
            'built_h' => 'Built H',
            'built_w' => 'Built W',
            'built_d' => 'Built D',
            'error_h' => 'Error H',
            'error_w' => 'Error W',
            'error_d' => 'Error D',
            'res_h' => 'Res H',
            'res_w' => 'Res W',
            'res_d' => 'Res D',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormDimension]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormDimension() {
        return $this->hasOne(TestFormDimension::className(), ['id' => 'form_dimension_id']);
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
        $matrix = ['panel_name', 'drawing_h', 'drawing_w', 'drawing_d'];

        $form = TestFormDimension::findOne($oldFormId);
        $oldDetails = $form->testDetailDimensions;
        $allSaved = true;

        foreach ($oldDetails as $detail) {
            $newDetail = new TestDetailDimension();
            foreach ($matrix as $attribute) {
                $newDetail->$attribute = $detail->$attribute;
            }
            $newDetail->form_dimension_id = $newFormId;
            if (!$newDetail->save()) {
                $allSaved = false;
            }
        }

        return $allSaved;
    }

}
