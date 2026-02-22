<?php

namespace frontend\models\working\project;

use Yii;
use frontend\models\working\contact\ContactMaster;
use common\models\User;
use frontend\models\working\project\ProspectMaster;

/**
 * This is the model class for table "prospect_detail".
 *
 * @property int $id
 * @property int $prospect_master
 * @property int|null $client_id
 * @property string|null $service
 * @property float|null $amount
 * @property string|null $pic_name
 * @property string|null $pic_contact
 * @property string|null $pic_email
 * @property string|null $email_attention
 * @property string|null $submission_date
 * @property int $is_awarded
 * @property int|null $created_by
 * @property string $created_at
 *
 * @property ContactMaster $client
 * @property User $createdBy
 * @property ProspectMaster $prospectMaster
 * @property ProspectDetailRevision[] $prospectDetailRevisions
 */
class ProspectDetail extends \yii\db\ActiveRecord {

    public $tempCompanyName;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prospect_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prospect_master', 'client_id', 'tempCompanyName', 'service'], 'required'],
            [['prospect_master', 'client_id', 'is_awarded', 'created_by'], 'integer'],
            [['amount'], 'number'],
            [['submission_date', 'created_at'], 'safe'],
            [['service', 'pic_name', 'pic_contact', 'pic_email', 'email_attention'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContactMaster::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['prospect_master'], 'exist', 'skipOnError' => true, 'targetClass' => ProspectMaster::className(), 'targetAttribute' => ['prospect_master' => 'id']],
            [['tempCompanyName'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prospect_master' => 'Prospect Master',
            'client_id' => 'Client ID',
            'service' => 'Service',
            'amount' => 'Amount',
            'pic_name' => 'Pic Name',
            'pic_contact' => 'Pic Contact',
            'pic_email' => 'Pic Email',
            'email_attention' => 'Email Attention',
            'submission_date' => 'Submission Date',
            'is_awarded' => 'Is Awarded',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(ContactMaster::className(), ['id' => 'client_id']);
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
     * Gets query for [[ProspectMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectMaster() {
        return $this->hasOne(ProspectMaster::className(), ['id' => 'prospect_master']);
    }

    /**
     * Gets query for [[ProspectDetailRevisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetailRevisions() {
        return $this->hasMany(ProspectDetailRevision::className(), ['prospect_detail_id' => 'id']);
    }

    public static function getDistinctService() {
        return ProspectDetail::find()
                        ->select(['service as value', 'service as id', 'service as label'])
                        ->orderBy(["service" => SORT_ASC])
                        ->distinct(true)
                        ->asArray()
                        ->all();
    }

}
