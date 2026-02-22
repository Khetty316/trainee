<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_item_functionality".
 *
 * @property int $id
 * @property int|null $detail_functionality_id
 * @property string|null $no
 * @property string|null $feeder_tag
 * @property int|null $voltage_apt
 * @property int|null $voltage_apt_sts
 * @property int|null $wiring_tc
 * @property int|null $wiring_tc_sts
 * @property string|null $group_num
 * @property int|null $order
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestDetailFunctionality $detailFunctionality
 */
class TestItemFunctionality extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_item_functionality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['detail_functionality_id', 'no', 'feeder_tag', 'voltage_apt', 'voltage_apt_sts', 'wiring_tc', 'wiring_tc_sts', 'group_num', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['detail_functionality_id', 'voltage_apt', 'voltage_apt_sts', 'wiring_tc', 'wiring_tc_sts', 'order', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['no', 'feeder_tag', 'group_num'], 'string', 'max' => 255],
            [['detail_functionality_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestDetailFunctionality::class, 'targetAttribute' => ['detail_functionality_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'detail_functionality_id' => 'Detail Functionality ID',
            'no' => 'No',
            'feeder_tag' => 'Feeder Tag',
            'voltage_apt' => 'Voltage At Power Terminal',
            'voltage_apt_sts' => 'Status',
            'wiring_tc' => 'Wiring Termination Connection',
            'wiring_tc_sts' => 'Status',
            'group_num' => 'Group Num',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[DetailFunctionality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailFunctionality() {
        return $this->hasOne(TestDetailFunctionality::class, ['id' => 'detail_functionality_id']);
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
        $matrix = ['no', 'feeder_tag', 'voltage_apt', 'wiring_tc', 'group_num', 'order'];

        $detail = TestDetailFunctionality::findOne($oldDetailId);
        $oldItems = $detail->testItemFunctionalities;
        $allSaved = true;

        foreach ($oldItems as $item) {
            $newItem = new TestItemFunctionality();
            foreach ($matrix as $attribute) {
                $newItem->$attribute = $item->$attribute;
            }
            $newItem->detail_functionality_id = $newDetailId;
            if (!$newItem->save()) {
                $allSaved = false;
            }
        }

        return $allSaved;
    }

}
