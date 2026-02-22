<?php

namespace frontend\models\common;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ref_currencies".
 *
 * @property string $currency_code
 * @property string|null $currency_name
 * @property string $currency_sign
 * @property float|null $exchange_rate
 * @property int $active
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class RefCurrencies extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_currencies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['currency_code', 'currency_sign'], 'required'],
            [['exchange_rate'], 'number'],
            [['active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['currency_code', 'currency_sign'], 'string', 'max' => 10],
            [['currency_name'], 'string', 'max' => 255],
            [['currency_code'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'currency_code' => 'Currency Code',
            'currency_name' => 'Currency Name',
            'currency_sign' => 'Currency Sign',
            'exchange_rate' => 'Exchange Rate',
            'active' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public static function getCurrencyActiveDropdownlist() {
        return \yii\helpers\ArrayHelper::map(RefCurrencies::findAll(["active" => "1"]), "currency_code", "currency_code");
    }

    public static function getActiveDropdownlist_by_id() {
        return \yii\helpers\ArrayHelper::map(RefCurrencies::findAll(["active" => "1"]), "currency_id", "currency_code");
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
