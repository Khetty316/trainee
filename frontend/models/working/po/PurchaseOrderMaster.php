<?php

namespace frontend\models\working\po;

use Yii;
use frontend\models\common\RefAddress;
use common\models\User;
use frontend\models\working\project\MasterProjects;
use frontend\models\common\RefCurrencies;
use frontend\models\working\mi\MasterIncomings;
use common\models\myTools\MyCommonFunction;
/**
 * This is the model class for table "purchase_order_master".
 *
 * @property int $po_id
 * @property string $po_number
 * @property string|null $po_date
 * @property string $project_code
 * @property float|null $amount
 * @property int $currency
 * @property string|null $po_material_desc
 * @property string|null $po_lead_time
 * @property string|null $po_etd
 * @property string|null $po_transporter
 * @property int|null $po_pic
 * @property int|null $po_address
 * @property int $po_receive_status
 * @property string|null $po_upload_file
 * @property int|null $quotation_master_id
 * @property string|null $remarks
 * @property int|null $onsite_receive_by
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $updated_by
 *
 * @property MasterIncomings[] $masterIncomings
 * @property RefAddress $poAddress
 * @property User $createdBy
 * @property RefCurrencies $currency0
 * @property User $onsiteReceiveBy
 * @property User $poPic
 * @property MasterProjects $projectCode
 * @property QuotationMasters $quotationMaster
 */
class PurchaseOrderMaster extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'purchase_order_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['po_number', 'project_code', 'po_pic', 'amount'], 'required'],
            [['po_date', 'created_at', 'update_at'], 'safe'],
            [['amount'], 'number'],
            [['currency', 'po_pic', 'po_address', 'po_receive_status', 'quotation_master_id', 'onsite_receive_by', 'created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['po_number', 'po_lead_time', 'po_etd'], 'string', 'max' => 100],
            [['project_code'], 'string', 'max' => 20],
            [['po_material_desc', 'po_transporter', 'po_upload_file'], 'string', 'max' => 255],
            [['po_number'], 'unique'],
            [['po_address'], 'exist', 'skipOnError' => true, 'targetClass' => RefAddress::className(), 'targetAttribute' => ['po_address' => 'address_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency' => 'currency_id']],
            [['onsite_receive_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['onsite_receive_by' => 'id']],
            [['po_pic'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['po_pic' => 'id']],
            [['project_code'], 'exist', 'skipOnError' => true, 'targetClass' => MasterProjects::className(), 'targetAttribute' => ['project_code' => 'project_code']],
            [['scannedFile'], 'file', 'skipOnEmpty' => false, 'when' => function($model) {
                    return $model->getIsNewRecord();
                }],
            ['scannedFile', 'file', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'po_id' => 'P.O. ID',
            'po_number' => 'P.O. Number',
            'po_date' => 'P.O. Date',
            'project_code' => 'Project Code',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'po_material_desc' => 'Material Description (Brief)',
            'po_lead_time' => 'Est. Lead Time (Days/Wks)',
            'po_etd' => 'ETD',
            'po_transporter' => 'Transporter',
            'po_pic' => 'Person In Charge',
            'po_address' => 'Address',
            'po_receive_status' => 'Receive?',
            'po_upload_file' => 'Upload File',
            'quotation_master_id' => 'Quotation Master ID',
            'remarks' => 'Remarks',
            'onsite_receive_by' => 'Onsite Receive By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->update_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[MasterIncomings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterIncomings() {
        return $this->hasMany(MasterIncomings::className(), ['po_id' => 'po_id']);
    }

    /**
     * Gets query for [[PoAddress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPoAddress() {
        return $this->hasOne(RefAddress::className(), ['address_id' => 'po_address']);
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
     * Gets query for [[Currency0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency0() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency']);
    }

    /**
     * Gets query for [[OnsiteReceiveBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOnsiteReceiveBy() {
        return $this->hasOne(User::className(), ['id' => 'onsite_receive_by']);
    }

    /**
     * Gets query for [[PoPic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPoPic() {
        return $this->hasOne(User::className(), ['id' => 'po_pic']);
    }

    /**
     * Gets query for [[ProjectCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCode() {
        return $this->hasOne(MasterProjects::className(), ['project_code' => 'project_code']);
    }

    /**
     * Gets query for [[QuotationMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationMaster() {
        return $this->hasOne(QuotationMasters::className(), ['id' => 'quotation_master_id']);
    }

    public function processAndSave() {
        if($this->po_date){
            $this->po_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->po_date);
        }
        if ($this->save()) {
            if ($this->validate()) {
                $this->po_upload_file = $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
                MyCommonFunction::mkDirIfNull(Yii::$app->params['po_file_path']);
                $this->scannedFile->saveAs(Yii::$app->params['po_file_path'] . $this->po_upload_file);
            }

            $this->update();
            return true;
        } else {
            return false;
        }
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(PurchaseOrderMaster::find()->all(), "po_number", "po_number");
    }

}
