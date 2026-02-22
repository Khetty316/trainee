<?php

namespace frontend\models\projectquotation;

use Yii;
use common\models\User;
use frontend\models\projectquotation\ProjectQTypes;
use frontend\models\common\RefCurrencies;
use frontend\models\common\RefProjectQShippingMode;

/**
 * This is the model class for table "project_q_revisions".
 *
 * @property int $id
 * @property int $project_q_type_id
 * @property string|null $revision_description
 * @property string|null $remark
 * @property int|null $currency_id
 * @property float|null $amount
 * @property int|null $incharged_by
 * @property int $is_active
 * @property int $is_finalized
 * @property int|null $finalized_by
 * @property string|null $q_material_offered
 * @property string|null $q_switchboard_standard
 * @property string|null $q_quotation
 * @property string|null $q_delivery_ship_mode
 * @property string|null $q_delivery_destination
 * @property string|null $q_delivery
 * @property string|null $q_validity
 * @property string|null $q_payment
 * @property string|null $q_remark
 * @property int $with_sst
 * @property int|null $show_breakdown
 * @property int|null $show_breakdown_price
 * @property float|null $discount_amt
 * @property int|null $discount_type 0 = Amount, 1 = Percentage
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectQPanels[] $projectQPanels
 * @property RefCurrencies $currency
 * @property User $inchargedBy
 * @property ProjectQTypes $projectQType
 * @property RefProjectQShippingMode $qDeliveryShipMode
 * @property ProjectQTypes[] $projectQTypes
 */
class ProjectQRevisions extends \yii\db\ActiveRecord {

    public $templateId;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_revisions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_q_type_id', 'templateId'], 'required'],
            [['project_q_type_id', 'currency_id', 'incharged_by', 'is_active', 'is_finalized', 'finalized_by', 'with_sst', 'show_breakdown', 'show_breakdown_price', 'discount_type', 'created_by', 'updated_by'], 'integer'],
            [['remark', 'q_material_offered', 'q_switchboard_standard', 'q_remark'], 'string'],
            [['amount', 'discount_amt'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['revision_description', 'q_quotation', 'q_delivery_destination', 'q_delivery', 'q_validity', 'q_payment'], 'string', 'max' => 255],
            [['q_delivery_ship_mode'], 'string', 'max' => 20],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['incharged_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['incharged_by' => 'id']],
            [['project_q_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQTypes::className(), 'targetAttribute' => ['project_q_type_id' => 'id']],
            [['q_delivery_ship_mode'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQShippingMode::className(), 'targetAttribute' => ['q_delivery_ship_mode' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_q_type_id' => 'Project Q Type ID',
            'revision_description' => 'Revision Name',
            'remark' => 'Internal Remarks',
            'currency_id' => 'Currency ID',
            'amount' => 'Amount',
            'incharged_by' => 'Incharged By',
            'is_active' => 'Is Active',
            'is_finalized' => 'Is Finalized',
            'finalized_by' => 'Finalized By',
            'q_material_offered' => 'Material Offered',
            'q_switchboard_standard' => 'Switchboard Standard',
            'q_quotation' => 'Quotation',
            'q_delivery_ship_mode' => 'Delivery Ship Mode',
            'q_delivery_destination' => 'Delivery Destination',
            'q_delivery' => 'Delivery',
            'q_validity' => 'Validity',
            'q_payment' => 'Payment',
            'q_remark' => 'Remark',
            'show_breakdown' => 'Show Sub Items',
            'show_breakdown_price' => 'Show Sub Items Price',
            'discount_amt' => 'Discount Amt',
            'discount_type' => 'Discount Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjectQPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQPanels() {
        return $this->hasMany(ProjectQPanels::className(), ['revision_id' => 'id']);
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
     * Gets query for [[InchargedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInchargedBy() {
        return $this->hasOne(User::className(), ['id' => 'incharged_by']);
    }

    /**
     * Gets query for [[ProjectQType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQType() {
        return $this->hasOne(ProjectQTypes::className(), ['id' => 'project_q_type_id']);
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
     * Gets query for [[ProjectQTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQTypes() {
        return $this->hasMany(ProjectQTypes::className(), ['active_revision_id' => 'id']);
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

    public function processAndSave() {
        $this->amount = 0;
        return $this->save(false);
    }

    public function processAndUpdate() {
        $this->q_delivery_ship_mode = trim($this->q_delivery_ship_mode) == "" ? null : $this->q_delivery_ship_mode;
        return $this->update(false);
    }

    public function updateRevisionAmount() {
        $sum = ProjectQPanels::find()->where(['revision_id' => $this->id])->sum('amount * quantity');
        $this->amount = $sum;
        return $this->update(false);
    }

    public function cloneRevisionsFromMother($motherRevisionId, $cloneRevisionNewName, $cloneRevisionNewRemark) {
        $motherRev = ProjectQRevisions::findOne($motherRevisionId);

        $this->revision_description = $cloneRevisionNewName;
        $this->remark = $cloneRevisionNewRemark;
        $this->project_q_type_id = $motherRev->project_q_type_id;
        $this->amount = $motherRev->amount;
        $this->incharged_by = $motherRev->incharged_by;
        $this->q_material_offered = $motherRev->q_material_offered;
        $this->q_switchboard_standard = $motherRev->q_switchboard_standard;
        $this->q_quotation = $motherRev->q_quotation;
        $this->q_delivery = $motherRev->q_delivery;
        $this->q_validity = $motherRev->q_validity;
        $this->q_payment = $motherRev->q_payment;
        $this->q_remark = $motherRev->q_remark;
        $this->with_sst = $motherRev->with_sst;
        $this->currency_id = $motherRev->currency_id;
        $this->q_delivery_destination = $motherRev->q_delivery_destination;
        $this->q_delivery_ship_mode = $motherRev->q_delivery_ship_mode;
        if ($this->save(false)) {
            $panels = $motherRev->projectQPanels;
            foreach ($panels as $key => $panel) {
                $newPanel = new ProjectQPanels();
                $newPanel->cloneFromMother($panel->id, $panel->panel_description, $this->id);
            }
        }
        return true;
    }

    public function createFromTemplate() {
        $motherRevisionTemplate = ProjectQRevisionsTemplate::findOne($this->templateId);
        $this->amount = $motherRevisionTemplate['amount'];
        $this->q_material_offered = $motherRevisionTemplate->q_material_offered;
        $this->q_switchboard_standard = $motherRevisionTemplate->q_switchboard_standard;
        $this->q_quotation = $motherRevisionTemplate->q_quotation;
        $this->q_delivery_ship_mode = $motherRevisionTemplate->q_delivery_ship_mode;
        $this->q_delivery_destination = $motherRevisionTemplate->q_delivery_destination;
        $this->q_delivery = $motherRevisionTemplate->q_delivery;
        $this->q_validity = $motherRevisionTemplate->q_validity;
        $this->q_payment = $motherRevisionTemplate->q_payment;
        $this->q_remark = $motherRevisionTemplate->q_remark;
        $this->with_sst = $motherRevisionTemplate->with_sst;
        $this->show_breakdown = $motherRevisionTemplate->show_breakdown;
        $this->show_breakdown_price = $motherRevisionTemplate->show_breakdown_price;
        if ($this->save(false)) {
            $panelTemplates = $motherRevisionTemplate->projectQPanelsTemplates;
            foreach ($panelTemplates as $key => $panelTemplate) {
                $newPanel = new ProjectQPanels();
                $newPanel->createFromTemplate($panelTemplate, $this->id);
            }
        }
        return true;

//        $this->amount
    }

}
