<?php

namespace frontend\models\asset;

use Yii;
use frontend\models\common\RefAssetCondition;
use frontend\models\common\RefAssetTrackingStatus;
use common\models\User;
use frontend\models\working\project\MasterProjects;
use frontend\models\common\RefAddress;
use frontend\models\common\RefArea;

/**
 * This is the model class for table "asset_tracking".
 *
 * @property int $id
 * @property int $asset_id
 * @property int|null $from_user
 * @property string|null $deliver_date
 * @property string|null $deliver_remark
 * @property int $receive_user
 * @property string $receive_proj_code
 * @property int $receive_area
 * @property int|null $receive_address
 * @property string $receive_condition
 * @property string|null $receive_date
 * @property string|null $receive_remark
 * @property string $request_status
 * @property int $alert_status
 * @property int $active_status
 * @property string $created_at
 * @property int $created_by
 *
 * @property AssetMaster $asset
 * @property User $createdBy
 * @property User $fromUser
 * @property RefAddress $receiveAddress
 * @property RefArea $receiveArea
 * @property RefAssetCondition $receiveCondition
 * @property RefAssetTrackingStatus $requestStatus
 * @property User $receiveUser
 */
class AssetTracking extends \yii\db\ActiveRecord {

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPT = 'accept';
    const STATUS_CANCEL = 'cancel';
    const STATUS_REJECT = 'reject';


    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'asset_tracking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['asset_id', 'receive_user', 'receive_proj_code', 'receive_area', 'receive_condition', 'request_status','deliver_date', 'receive_date'], 'required'],
            [['asset_id', 'from_user', 'receive_user', 'receive_area', 'receive_address', 'alert_status', 'active_status', 'created_by'], 'integer'],
            [['deliver_date', 'created_at'], 'safe'],
            [['deliver_remark', 'receive_remark'], 'string'],
            [['receive_proj_code'], 'string', 'max' => 20],
            [['receive_condition', 'request_status'], 'string', 'max' => 15],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => AssetMaster::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['from_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_user' => 'id']],
            [['receive_address'], 'exist', 'skipOnError' => true, 'targetClass' => RefAddress::className(), 'targetAttribute' => ['receive_address' => 'address_id']],
            [['receive_area'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['receive_area' => 'area_id']],
            [['receive_condition'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetCondition::className(), 'targetAttribute' => ['receive_condition' => 'code']],
            [['request_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetTrackingStatus::className(), 'targetAttribute' => ['request_status' => 'code']],
            [['receive_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['receive_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'asset_id' => 'Asset ID',
            'from_user' => 'From User',
            'deliver_date' => 'Deliver Date',
            'deliver_remark' => 'Deliver Remark',
            'receive_user' => 'Receive User',
            'receive_proj_code' => 'Receive Proj Code',
            'receive_area' => 'Receive Area',
            'receive_address' => 'Receive Address',
            'receive_condition' => 'Receive Condition',
            'receive_date' => 'Receive Date',
            'receive_remark' => 'Receive Remark',
            'request_status' => 'Request Status',
            'alert_status' => 'Alert Status',
            'active_status' => 'Active Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Asset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsset() {
        return $this->hasOne(AssetMaster::className(), ['id' => 'asset_id']);
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
     * Gets query for [[FromUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser() {
        return $this->hasOne(User::className(), ['id' => 'from_user']);
    }

    /**
     * Gets query for [[ReceiveAddress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveAddress() {
        return $this->hasOne(RefAddress::className(), ['address_id' => 'receive_address']);
    }

    /**
     * Gets query for [[ReceiveArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveArea() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'receive_area']);
    }

    /**
     * Gets query for [[ReceiveCondition]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveCondition() {
        return $this->hasOne(RefAssetCondition::className(), ['code' => 'receive_condition']);
    }

    /**
     * Gets query for [[RequestStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestStatus() {
        return $this->hasOne(RefAssetTrackingStatus::className(), ['code' => 'request_status']);
    }

    /**
     * Gets query for [[ReceiveUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveUser() {
        return $this->hasOne(User::className(), ['id' => 'receive_user']);
    }

//    MasterProjects
    public function getMasterProjects() {
        return $this->hasOne(MasterProjects::className(), ['project_code' => 'receive_proj_code']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        if ($this->receive_date != '') {
            $this->receive_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->receive_date);
        }
        if ($this->deliver_date != '') {
            $this->deliver_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->deliver_date);
        }

        return $this->save(false);
    }

}
