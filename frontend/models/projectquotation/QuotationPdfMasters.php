<?php

namespace frontend\models\projectquotation;

use Yii;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefCurrencies;
use frontend\models\common\RefProjectQShippingMode;
use common\models\User;

/**
 * This is the model class for table "quotation_pdf_masters".
 *
 * @property int $id
 * @property string $quotation_no
 * @property int $project_q_client_id
 * @property int $revision_id
 * @property string|null $to_company
 * @property string|null $to_pic
 * @property string|null $to_tel_no
 * @property string|null $to_fax_no
 * @property string|null $q_from
 * @property string|null $q_your_ref
 * @property string $q_date
 * @property string|null $proj_title
 * @property int|null $proj_q_rev_id
 * @property int|null $with_sst
 * @property int $currency_id
 * @property int|null $show_breakdown
 * @property int|null $show_breakdown_price
 * @property float|null $discount_amt
 * @property int|null $discount_type 0 = Amount, 1 = Percentage
 * @property int $show_panel_description
 * @property string|null $q_material_offered
 * @property string|null $q_switchboard_standard
 * @property string|null $q_quotation
 * @property string|null $q_delivery_ship_mode
 * @property string|null $q_delivery_destination
 * @property string|null $q_delivery
 * @property string|null $q_validity
 * @property string|null $q_payment
 * @property string|null $q_remark
 * @property string|null $filename
 * @property string|null $file_type
 * @property int|null $file_size
 * @property resource|null $file_blob
 * @property int|null $prepared_by
 * @property int|null $approved_by
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $prepared_by_sign
 * @property string|null $approved_by_sign
 * @property int|null $md_approval_status 1 = get approval, 2 = approved
 * @property string|null $md_approval_date
 * @property int|null $md_user_id
 *
 * @property RefCurrencies $currency
 * @property ProjectQClients $projectQClient
 * @property RefProjectQShippingMode $qDeliveryShipMode
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $mdUser
 */
class QuotationPdfMasters extends \yii\db\ActiveRecord {

    CONST QuotationPrefix = "Q";
    CONST QUOTATION_GET_DIRECTOR_APPROVAL = 1;
    CONST QUOTATION_DIRECTOR_APPROVED = 2;
    
    public $discount_amt_from_con;
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'quotation_pdf_masters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['quotation_no', 'project_q_client_id', 'revision_id', 'q_date'], 'required'],
//            ['with_sst', 'filter', 'filter' => 'intval'],
            [['project_q_client_id', 'revision_id', 'proj_q_rev_id', 'with_sst', 'currency_id', 'show_breakdown', 'show_breakdown_price', 'discount_type', 'show_panel_description', 'file_size', 'prepared_by', 'approved_by', 'created_by', 'updated_by', 'md_approval_status', 'md_user_id'], 'integer'],
            ['with_sst', 'default', 'value' => 0],
            [['q_date', 'created_at', 'updated_at', 'md_approval_date'], 'safe'],
            [['discount_amt'], 'number'],
            [['q_material_offered', 'q_switchboard_standard', 'q_remark', 'file_blob'], 'string'],
            [['quotation_no', 'to_company', 'to_pic', 'to_tel_no', 'to_fax_no', 'q_from', 'q_your_ref', 'q_quotation', 'q_delivery_destination', 'q_delivery', 'q_validity', 'q_payment', 'filename', 'file_type'], 'string', 'max' => 255],
            [['proj_title'], 'string', 'max' => 500],
            [['q_delivery_ship_mode'], 'string', 'max' => 20],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['project_q_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQClients::className(), 'targetAttribute' => ['project_q_client_id' => 'id']],
            [['q_delivery_ship_mode'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQShippingMode::className(), 'targetAttribute' => ['q_delivery_ship_mode' => 'code']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['md_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['md_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_no' => 'Quotation No.',
            'project_q_client_id' => 'Clients',
            'revision_id' => 'Revision ID',
            'to_company' => 'To Company',
            'to_pic' => 'To Pic',
            'to_tel_no' => 'To Tel No',
            'to_fax_no' => 'To Fax No',
            'q_from' => 'From',
            'q_your_ref' => 'Your Ref',
            'q_date' => 'Date',
            'proj_title' => 'Project Name',
            'proj_q_rev_id' => 'Proj Rev ID',
            'with_sst' => 'With Sst',
            'currency_id' => 'Currency',
            'show_breakdown' => 'Show Sub Items',
            'show_breakdown_price' => 'Show Sub Items Price',
            'discount_amt' => 'Discount Amt',
            'discount_type' => 'Discount Type',
            'show_panel_description' => 'Show Panel Description',
            'q_material_offered' => 'Material Offered',
            'q_switchboard_standard' => 'Switchboard Standard',
            'q_quotation' => 'Quotation',
            'q_delivery_ship_mode' => 'Delivery Ship Mode',
            'q_delivery_destination' => 'Delivery Destination',
            'q_delivery' => 'Delivery',
            'q_validity' => 'Validity',
            'q_payment' => 'Payment',
            'q_remark' => 'Remark',
            'filename' => 'Filename',
            'file_type' => 'File Type',
            'file_size' => 'File Size',
            'file_blob' => 'File Blob',
            'prepared_by' => 'Prepared By',
            'approved_by' => 'Approved By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'prepared_by_sign' => 'Prepared By Sign',
            'approved_by_sign' => 'Approved By Sign',
            'md_approval_status' => 'Md Approval Status',
            'md_approval_date' => 'Md Approval Date',
            'md_user_id' => 'Md User ID',
        ];
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * Gets query for [[ProjectQClient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQClient() {
        return $this->hasOne(ProjectQClients::className(), ['id' => 'project_q_client_id']);
    }

    /**
     * Gets query for [[QDeliveryShipMode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQDeliveryShipMode() {
        return $this->hasOne(RefProjectQShippingMode::className(), ['code' => 'q_delivery_ship_mode']);
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

    /**
     * Gets query for [[MdUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMdUser() {
        return $this->hasOne(User::className(), ['id' => 'md_user_id']);
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

    public function copyRevisionAndClient($revision, $client) {
        $projectQMaster = $revision->projectQType->project;
        $this->quotation_no = $this->generateCompleteQuotationNo($projectQMaster, $revision, $client);
        $this->revision_id = $revision->id;
        $this->to_company = $client->company_name;
        $this->to_pic = $client->contact_person;
        $this->to_tel_no = $client->contact_number;
        $this->to_fax_no = $client->contact_fax;
        $this->with_sst = $revision->with_sst;
        $this->q_material_offered = $revision->q_material_offered;
        $this->q_switchboard_standard = $revision->q_switchboard_standard;
        $this->q_quotation = $revision->q_quotation;
        $this->q_delivery = $revision->q_delivery;
        $this->q_validity = $revision->q_validity;
        $this->q_payment = $revision->q_payment;
        $this->q_remark = $revision->q_remark;
        $this->show_breakdown = $revision->show_breakdown;
        $this->show_breakdown_price = $revision->show_breakdown_price;
        $this->currency_id = $revision->currency_id;
        $this->q_delivery_destination = $revision->q_delivery_destination;
        $this->q_delivery_ship_mode = $revision->q_delivery_ship_mode;
        $this->discount_amt = $revision->discount_amt;
        $this->discount_type = $revision->discount_type;
    }

//    public function processAndSave() {
//        if ($this->q_date) {
//            $this->q_date = MyFormatter::changeDateFormat_readToDB($this->q_date);
//        }
//        if (!$this->save()) {
//            \common\models\myTools\Mydebug::dumpFileW($this->getErrors());
//        }
////        return $this->save();
//    }
    
    public function processAndSave() {
        if ($this->q_date) {
            $this->q_date = MyFormatter::changeDateFormat_readToDB($this->q_date);
        }

        if (!$this->save()) {
            return false;
        }

        return true;
    }

    public function savePdfToBlob($dir, $filename) {
        $this->file_type = "application/pdf";
        $this->file_size = filesize($dir . $filename);
        $this->file_blob = file_get_contents($dir . $filename);
    }

    static public function linkQuotation($clientId, $revisionId) {
        return QuotationPdfMasters::find()->where(['client_id' => $clientId, 'revision_id' => $revisionId])->one();
    }

    private function generateCompleteQuotationNo($projectQMaster, $revision, $client) {
//        $returnStr = self::QuotationPrefix . $projectQMaster->quotation_no . "/" . $projectQMaster['company_group_code'] . "/" . date('y') . "-";
        $returnStr = self::QuotationPrefix . $projectQMaster->quotation_display_no . "-";
        $returnStr .= $client->client_code . "-"
                . substr($revision->projectQType->type0->project_type_name, 0, 1) . "-"
                . str_replace("Revision ", 'R', $revision->revision_description);

        return $returnStr;
    }
}
