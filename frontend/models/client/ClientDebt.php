<?php

namespace frontend\models\client;

use Yii;
use common\models\User;

/**
 * This is the model class for table "client_debt".
 *
 * @property int $id
 * @property int|null $client_id
 * @property string|null $tk_group_code tk, tke, tkm
 * @property int|null $month
 * @property int|null $year
 * @property float|null $balance
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property Clients $client
 * @property User $createdBy
 * @property User $updatedBy
 */
class ClientDebt extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'client_debt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['client_id', 'year', 'created_by', 'updated_by'], 'integer'],
            [['balance'], 'number'],
            [['created_at', 'updated_at', 'month'], 'safe'],
      [['tk_group_code', 'month', 'year'], 'required'],
            [['tk_group_code', 'month', 'year'], 'safe'],
            [['tk_group_code'], 'string', 'max' => 10],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'tk_group_code' => 'Company Group',
            'month' => 'Month',
            'year' => 'Year',
            'balance' => 'Balance (RM)',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getCompanyGroup() {
        return $this->hasOne(
                        \frontend\models\common\RefCompanyGroupList::class,
                        ['code' => 'tk_group_code']
                );
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
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
}
