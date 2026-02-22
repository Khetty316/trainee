<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_state".
 *
 * @property int $state_id
 * @property string $state_name
 * @property string|null $state_capital
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ContactMaster[] $contactMasters
 * @property RefArea[] $refAreas
 */
class RefState extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_state';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['state_name'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['state_name', 'state_capital'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'state_id' => 'State ID',
            'state_name' => 'State Name',
            'state_capital' => 'State Capital',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[ContactMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactMasters() {
        return $this->hasMany(ContactMaster::className(), ['state' => 'state_id']);
    }

    /**
     * Gets query for [[RefAreas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefAreas() {
        return $this->hasMany(RefArea::className(), ['state_id' => 'state_id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefState::find()->orderBy(['state_name' => SORT_ASC])->all(), "state_id", "state_name");
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

    public function createNew($stateName, $stateCapital = '') {
        $this->state_name = $stateName;
        if ($stateCapital != "") {
            $this->state_capital = $stateCapital;
        }
        return $this->save();
    }

}
