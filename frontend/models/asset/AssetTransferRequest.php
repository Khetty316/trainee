<?php

namespace frontend\models\asset;

use Yii;
use frontend\models\common\RefAssetApprovalStatus;
use common\models\User;

/**
 * This is the model class for table "asset_transfer_request".
 *
 * @property int $id
 * @property int|null $requestor
 * @property int $asset_id
 * @property string|null $remark
 * @property string|null $request_status
 * @property int|null $response_by
 * @property string|null $response_time
 * @property int $active_status
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster $asset
 * @property RefAssetApprovalStatus $requestStatus
 * @property User $requestor0
 */
class AssetTransferRequest extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'asset_transfer_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['requestor', 'asset_id', 'response_by', 'active_status', 'created_by'], 'integer'],
            [['asset_id'], 'required'],
            [['remark'], 'string'],
            [['response_time', 'created_at'], 'safe'],
            [['request_status'], 'string', 'max' => 30],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => AssetMaster::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['request_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetApprovalStatus::className(), 'targetAttribute' => ['request_status' => 'code']],
            [['requestor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requestor' => 'Requestor',
            'asset_id' => 'Asset ID',
            'remark' => 'Remark',
            'request_status' => 'Request Status',
            'response_by' => 'Response By',
            'response_time' => 'Response Time',
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
     * Gets query for [[RequestStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestStatus() {
        return $this->hasOne(RefAssetApprovalStatus::className(), ['code' => 'request_status']);
    }

    /**
     * Gets query for [[Requestor0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor0() {
        return $this->hasOne(User::className(), ['id' => 'requestor']);
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
        $this->request_status = RefAssetApprovalStatus::STATUS_PENDING;
        $this->active_status = 1;
        return $this->save();
    }

    public function setCancel() {
        if ($this->active_status == 1 && $this->request_status == RefAssetApprovalStatus::STATUS_PENDING) {
            $this->request_status = RefAssetApprovalStatus::STATUS_CANCEL;
            $this->active_status = 0;
            $this->response_by = Yii::$app->user->id;
            $this->response_time = new \yii\db\Expression('NOW()');
            return $this->update();
        }
        return false;
    }

    public function setComplete() {
        if ($this->active_status == 1 && $this->request_status == RefAssetApprovalStatus::STATUS_PENDING) {
            $this->request_status = RefAssetApprovalStatus::STATUS_APPROVE;
            $this->active_status = 0;
            $this->response_by = Yii::$app->user->id;
            $this->response_time = new \yii\db\Expression('NOW()');
            return $this->update();
        }
        return false;
    }

}
