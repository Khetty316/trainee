<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_detail_conform".
 *
 * @property int $id
 * @property int|null $form_component_id
 * @property string|null $non_conform
 * @property string|null $remark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormComponent $formComponent
 */
class TestDetailConform extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_conform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_component_id', 'non_conform', 'remark', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_component_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['non_conform', 'remark'], 'string', 'max' => 255],
            [['form_component_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormComponent::class, 'targetAttribute' => ['form_component_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_component_id' => 'Form Component ID',
            'non_conform' => 'Non Conform',
            'remark' => 'Remark',
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
        return $this->hasOne(TestFormComponent::class, ['id' => 'form_component_id']);
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
