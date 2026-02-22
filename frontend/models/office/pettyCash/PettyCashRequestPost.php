<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;

/**
 * This is the model class for table "petty_cash_request_post".
 *
 * @property int $id
 * @property int|null $pc_request_master_id
 * @property int|null $pc_request_pre_id
 * @property float|null $receipt_amount
 * @property int|null $status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property int|null $responsed_by
 * @property string|null $responsed_at
 * @property string|null $responsed_remark
 * @property float|null $amount_approved
 *
 * @property PettyCashRequestMaster $pcRequestMaster
 * @property PettyCashRequestPre $pcRequestPre
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deletedBy
 * @property User $responsedBy
 * @property PettyCashRequestPostAttachment[] $pettyCashRequestPostAttachments
 */
class PettyCashRequestPost extends \yii\db\ActiveRecord
{
    public $attachments;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'petty_cash_request_post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pc_request_master_id', 'pc_request_pre_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'responsed_by'], 'integer'],
            [['receipt_amount', 'amount_approved'], 'number'],
            [['created_at', 'updated_at', 'deleted_at', 'responsed_at'], 'safe'],
            [['responsed_remark'], 'string', 'max' => 255],
            [['pc_request_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PettyCashRequestMaster::className(), 'targetAttribute' => ['pc_request_master_id' => 'id']],
            [['pc_request_pre_id'], 'exist', 'skipOnError' => true, 'targetClass' => PettyCashRequestPre::className(), 'targetAttribute' => ['pc_request_pre_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responsed_by' => 'id']],
            [['attachments'], 'file', 'extensions' => 'pdf', 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pc_request_master_id' => 'Pc Request Master ID',
            'pc_request_pre_id' => 'Pc Request Pre ID',
            'receipt_amount' => 'Receipt Amount',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
            'responsed_by' => 'Responsed By',
            'responsed_at' => 'Responsed At',
            'responsed_remark' => 'Responsed Remark',
            'amount_approved' => 'Amount Approved',
        ];
    }

    /**
     * Gets query for [[PcRequestMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPcRequestMaster()
    {
        return $this->hasOne(PettyCashRequestMaster::className(), ['id' => 'pc_request_master_id']);
    }

    /**
     * Gets query for [[PcRequestPre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPcRequestPre()
    {
        return $this->hasOne(PettyCashRequestPre::className(), ['id' => 'pc_request_pre_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[ResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'responsed_by']);
    }

    /**
     * Gets query for [[PettyCashRequestPostAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPettyCashRequestPostAttachments()
    {
        return $this->hasMany(PettyCashRequestPostAttachment::className(), ['pc_request_post_id' => 'id']);
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
