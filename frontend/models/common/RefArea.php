<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_area".
 *
 * @property int $area_id
 * @property string $area_name
 * @property int|null $state_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $updated_by
 *
 * @property RefAddress[] $refAddresses
 * @property RefState $state
 */
class RefArea extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['area_name'], 'required'],
            [['state_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['area_name'], 'string', 'max' => 255],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefState::className(), 'targetAttribute' => ['state_id' => 'state_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'state_id' => 'State ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[RefAddresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefAddresses() {
        return $this->hasMany(RefAddress::className(), ['area_id' => 'area_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState() {
        return $this->hasOne(RefState::className(), ['state_id' => 'state_id']);
    }

//    public static function getActiveDropDownList() {
//        return \yii\helpers\ArrayHelper::map(RefArea::findAll(["active" => "1", "is_po_address" => "1"]), "address_id", "address_name");
//    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefArea::find()->orderBy(['area_name' => SORT_ASC])->all(), "area_id", "area_name");
    }

    public static function getAutocompleteList() {
        return RefArea::find()->select(['area_name as value', 'area_id as id', 'area_name as label'])
                        ->orderBy(['area_name' => SORT_ASC])
                        ->asArray()
                        ->all();
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function createNew($areaName, $stateId = '') {
        $this->area_name = $areaName;
        if ($stateId != "") {
            $this->state_id = $stateId;
        }
        return $this->save();
    }

}
