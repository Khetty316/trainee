<?php

namespace frontend\models\asset;

use Yii;
use frontend\models\common\RefAssetCategory;
use frontend\models\common\RefAssetSubCategory;
use frontend\models\common\RefAssetOwnType;
use frontend\models\common\RefAssetCondition;
use common\models\User;
use common\models\myTools\MyCommonFunction;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefAssetApprovalStatus;

/**
 * This is the model class for table "asset_master".
 *
 * @property int $id
 * @property string|null $asset_idx_no
 * @property int $asset_category
 * @property int $asset_sub_category
 * @property string|null $file_image
 * @property string|null $file_invoice_image
 * @property int|null $purchased_by
 * @property string $own_type
 * @property float|null $rental_fee
 * @property int $idle_sts
 * @property string $description
 * @property string $brand
 * @property string|null $model
 * @property string|null $specification
 * @property string|null $remarks
 * @property string $condition
 * @property float|null $cost
 * @property string|null $warranty_due_date
 * @property int $active_sts
 * @property string $created_at
 * @property int $created_by
 * @property string $approval_status
 * @property string|null $approved_at
 * @property int|null $approved_by
 *
 * @property RefAssetApprovalStatus $approvalStatus
 * @property RefAssetCategory $assetCategory
 * @property User $createdBy
 * @property RefAssetCondition $condition0
 * @property RefAssetOwnType $ownType
 * @property User $purchasedBy
 * @property RefAssetSubCategory $assetSubCategory
 * @property AssetService[] $assetServices
 * @property AssetTracking[] $assetTrackings
 */
class AssetMaster extends \yii\db\ActiveRecord {

    public $fileImage;
    public $fileInvoiceImage;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'asset_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['asset_category', 'asset_sub_category', 'own_type', 'description', 'brand', 'condition', 'created_by', 'approval_status'], 'required'],
            [['asset_category', 'asset_sub_category', 'purchased_by', 'idle_sts', 'active_sts', 'created_by', 'approved_by'], 'integer'],
            [['rental_fee', 'cost'], 'number'],
            [['specification', 'remarks'], 'string'],
            [['warranty_due_date', 'created_at', 'approved_at'], 'safe'],
            [['asset_idx_no'], 'string', 'max' => 30],
            [['file_image', 'file_invoice_image', 'description'], 'string', 'max' => 255],
            [['own_type'], 'string', 'max' => 8],
            [['brand', 'model'], 'string', 'max' => 200],
            [['condition', 'approval_status'], 'string', 'max' => 15],
            [['approval_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetApprovalStatus::className(), 'targetAttribute' => ['approval_status' => 'code']],
            [['asset_category'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetCategory::className(), 'targetAttribute' => ['asset_category' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['condition'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetCondition::className(), 'targetAttribute' => ['condition' => 'code']],
            [['own_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetOwnType::className(), 'targetAttribute' => ['own_type' => 'code']],
            [['purchased_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['purchased_by' => 'id']],
            [['asset_sub_category'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetSubCategory::className(), 'targetAttribute' => ['asset_sub_category' => 'id']],
            ['fileImage', 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'maxFiles' => 1, 'skipOnEmpty' => true],
            ['fileInvoiceImage', 'file', 'extensions' => 'png, jpg, pdf, jpeg', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'maxFiles' => 1, 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'asset_idx_no' => 'Index No.',
            'asset_category' => 'Category',
            'asset_sub_category' => 'Sub Category',
            'file_image' => 'Image',
            'file_invoice_image' => 'Invoice Image',
            'purchased_by' => 'Purchased By',
            'own_type' => 'Own Type',
            'rental_fee' => 'Rental Fee (RM)',
            'idle_sts' => 'Idle?',
            'description' => 'Description',
            'brand' => 'Brand',
            'model' => 'Model',
            'specification' => 'Specification',
            'remarks' => 'Remarks',
            'condition' => 'Condition',
            'cost' => 'Cost',
            'warranty_due_date' => 'Warranty Due Date',
            'active_sts' => 'Active Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'approval_status' => 'Approval Status',
            'approved_at' => 'Approved At',
            'approved_by' => 'Approved By',
        ];
    }

    /**
     * Gets query for [[ApprovalStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovalStatus() {
        return $this->hasOne(RefAssetApprovalStatus::className(), ['code' => 'approval_status']);
    }

    /**
     * Gets query for [[AssetCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetCategory() {
        return $this->hasOne(RefAssetCategory::className(), ['id' => 'asset_category']);
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
     * Gets query for [[Condition0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCondition0() {
        return $this->hasOne(RefAssetCondition::className(), ['code' => 'condition']);
    }

    /**
     * Gets query for [[OwnType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwnType() {
        return $this->hasOne(RefAssetOwnType::className(), ['code' => 'own_type']);
    }

    /**
     * Gets query for [[PurchasedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchasedBy() {
        return $this->hasOne(User::className(), ['id' => 'purchased_by']);
    }

    /**
     * Gets query for [[AssetSubCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetSubCategory() {
        return $this->hasOne(RefAssetSubCategory::className(), ['id' => 'asset_sub_category']);
    }

    /**
     * Gets query for [[AssetServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetServices() {
        return $this->hasMany(AssetService::className(), ['asset_id' => 'id']);
    }

    /**
     * Gets query for [[AssetTrackings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetTrackings() {
        return $this->hasMany(AssetTracking::className(), ['asset_id' => 'id']);
    }

    public function getAssetTrackingsActive() {
        return AssetTracking::find()->where(['asset_id' => $this->id, 'active_status' => 1])->one();
    }

    public function getAssetTrackingsPending() {
        return AssetTracking::find()->where(['asset_id' => $this->id, 'request_status' => 'pending'])->one();
    }
    
    public function getAssetRequestTransfer() {
        return AssetTransferRequest::find()->where(['asset_id' => $this->id, 'active_status' => 1,'request_status'=> RefAssetApprovalStatus::STATUS_PENDING])->all();
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
        if ($this->warranty_due_date != "") {
            $this->warranty_due_date = MyFormatter::fromDateRead_toDateSQL($this->warranty_due_date);
        }
        $this->created_by = Yii::$app->user->id;
        if ($this->save()) {

            $filePath = Yii::$app->params['asset_folder'] . '/' . $this->id . '/';

            if ($this->fileImage) {
                if ($this->file_image && file_exists($filePath . $this->file_image)) {
                    unlink($filePath . $this->file_image);
                }
                $this->file_image = date('Ymdhis', time()) . '-' . $this->fileImage->baseName . '.' . $this->fileImage->extension;
                MyCommonFunction::saveFile($this->fileImage, $filePath, $this->file_image);
            }

            if ($this->fileInvoiceImage) {
                if ($this->file_invoice_image && file_exists($filePath . $this->file_invoice_image)) {
                    unlink($filePath . $this->file_invoice_image);
                }
                $this->file_invoice_image = date('Ymdhis', time()) . '-' . $this->fileInvoiceImage->baseName . '.' . $this->fileInvoiceImage->extension;
                MyCommonFunction::saveFile($this->fileInvoiceImage, $filePath, $this->file_invoice_image);
            }
            $this->update(false);
            return true;
        } else {
            \common\models\myTools\Mydebug::dumpFileA($this->errors);
            return false;
        }
    }

    public function generateAssetIdxNo() {
        $idxNo = trim(strtoupper(substr($this->assetCategory->name, 0, 2) . substr($this->assetSubCategory->name, 0, 2)));
        $idxNo .= date('Ymdhis', time());
        $assetsTotal = AssetMaster::find()->where('asset_sub_category=' . $this->asset_sub_category)->andWhere('asset_idx_no IS NOT NULL')->sum("1") + 1;
        for ($i = strlen((string) $assetsTotal); $i <= 4; $i++) {
            $assetsTotal = '0' . $assetsTotal;
        }
        $idxNo .= $assetsTotal;
        $this->asset_idx_no = $idxNo;
        return true;
//        return $idxNo;
    }

    private function initiateTrackingRecord() {
        $tracking = new AssetTracking();
        $tracking->asset_id = $this->id;
        $tracking->receive_user = $this->purchased_by;
//        $tracking->
    }

    public function setApprove() {
        $this->approval_status = RefAssetApprovalStatus::STATUS_APPROVE;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->approved_by = Yii::$app->user->id;
        $this->active_sts = 1;
        $this->generateAssetIdxNo();
        return true;
    }

    public function setApproveAndUpdate() {
        if ($this->approval_status != RefAssetApprovalStatus::STATUS_PENDING) {
            return false;
        }

        $this->setApprove();
        return $this->update();
    }

    public function setRejectAndUpdate() {
        if ($this->approval_status != RefAssetApprovalStatus::STATUS_PENDING) {
            return false;
        }
        $this->approval_status = RefAssetApprovalStatus::STATUS_REJECT;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->approved_by = Yii::$app->user->id;
        $this->active_sts = 0;
//        $this->generateAssetIdxNo();
        return $this->update();
    }

    public function setCancelAndUpdate() {
        if ($this->approval_status != RefAssetApprovalStatus::STATUS_PENDING) {
            return false;
        }
        $this->approval_status = RefAssetApprovalStatus::STATUS_CANCEL;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->approved_by = Yii::$app->user->id;
        $this->active_sts = 0;
//        $this->generateAssetIdxNo();
        return $this->update();
    }

}
