<?php

namespace frontend\models\test;

use Yii;
use frontend\models\test\TestDetailComponent;

/**
 * This is the model class for table "test_item_comp_other".
 *
 * @property int $id
 * @property int|null $detail_component_id
 * @property string|null $attribute
 * @property string|null $value
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestDetailComponent $detailComponent
 */
class TestItemCompOther extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_item_comp_other';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['detail_component_id', 'attribute', 'value', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['detail_component_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['attribute', 'value'], 'string', 'max' => 255],
            [['detail_component_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestDetailComponent::class, 'targetAttribute' => ['detail_component_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'detail_component_id' => 'Detail Component ID',
            'attribute' => 'Attribute',
            'value' => 'Value',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[DetailComponent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailComponent() {
        return $this->hasOne(TestDetailComponent::class, ['id' => 'detail_component_id']);
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

    public function copyOver($oldDetailId, $newDetailId) {
        $matrix = ['attribute', 'value'];

        $detail = TestDetailComponent::findOne($oldDetailId);
        $oldItems = $detail->testItemCompOthers;
        $allSaved = true;

        foreach ($oldItems as $item) {
            $newItem = new TestItemCompOther();
            foreach ($matrix as $attribute) {
                $newItem->$attribute = $item->$attribute;
            }
            $newItem->detail_component_id = $newDetailId;
            if (!$newItem->save()) {
                $allSaved = false;
            }
        }

        return $allSaved;
    }

}
