<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\common\RefCurrencies;
use common\models\User;

/**
 * This is the model class for table "project_q_revisions_template".
 *
 * @property int $id
 * @property int|null $revision_copy_master
 * @property string|null $revision_description
 * @property string|null $remark
 * @property int|null $currency_id
 * @property float|null $amount
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
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_active
 * @property int|null $deactivated_by
 * @property string|null $deactivated_at
 * @property int|null $is_active
 *
 * @property ProjectQPanelsTemplate[] $projectQPanelsTemplates
 * @property RefCurrencies $currency
 * @property User $deactivatedBy
 * @property User $createdBy
 */
class ProjectQRevisionsTemplate extends \yii\db\ActiveRecord {

    CONST IS_ACTIVE = [0 => 'No', 1 => 'Yes'];
    CONST IS_ACTIVE_HTML = [
        0 => '<span class="text-danger text-center">No</span>',
        1 => '<span class="text-success text-center">Yes</span>',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_revisions_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['revision_copy_master', 'currency_id', 'with_sst', 'show_breakdown', 'show_breakdown_price', 'created_by', 'updated_by', 'is_active', 'deactivated_by'], 'integer'],
            [['remark', 'q_material_offered', 'q_switchboard_standard', 'q_remark'], 'string'],
            [['amount'], 'number'],
            [['created_at', 'updated_at', 'deactivated_at'], 'safe'],
            [['revision_description', 'q_quotation', 'q_delivery_destination', 'q_delivery', 'q_validity', 'q_payment'], 'string', 'max' => 255],
            [['q_delivery_ship_mode'], 'string', 'max' => 20],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['deactivated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deactivated_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'revision_copy_master' => 'Revision Copy Master',
            'revision_description' => 'Template Name',
            'remark' => 'Internal Remark',
            'currency_id' => 'Currency',
            'amount' => 'Amount',
            'q_material_offered' => 'Material Offered',
            'q_switchboard_standard' => 'Switchboard Standard',
            'q_quotation' => 'Quotation',
            'q_delivery_ship_mode' => 'Delivery Ship Mode',
            'q_delivery_destination' => 'Delivery Destination',
            'q_delivery' => 'Delivery',
            'q_validity' => 'Validity',
            'q_payment' => 'Payment',
            'q_remark' => 'Remark',
            'with_sst' => 'With Sst',
            'show_breakdown' => 'Show Sub Items',
            'show_breakdown_price' => 'Show Sub Items Price',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active?',
            'deactivated_by' => 'Deactivated By',
            'deactivated_at' => 'Deactivated At',
        ];
    }

    /**
     * Gets query for [[ProjectQPanelsTemplates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQPanelsTemplates() {
        return $this->hasMany(ProjectQPanelsTemplate::className(), ['revision_template_id' => 'id']);
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
     * Gets query for [[DeactivatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeactivatedBy() {
        return $this->hasOne(User::className(), ['id' => 'deactivated_by']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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

    public function setAsTemplate($revision, $templateName) {
        $this->revision_copy_master = $revision->id;
        $this->revision_description = $templateName;
        $this->currency_id = $revision->currency_id;
        $this->amount = $revision->amount;
        $this->q_material_offered = $revision->q_material_offered;
        $this->q_switchboard_standard = $revision->q_switchboard_standard;
        $this->q_quotation = $revision->q_quotation;
        $this->q_delivery_ship_mode = $revision->q_delivery_ship_mode;
        $this->q_delivery_destination = $revision->q_delivery_destination;
        $this->q_delivery = $revision->q_delivery;
        $this->q_validity = $revision->q_validity;
        $this->q_payment = $revision->q_payment;
        $this->q_remark = $revision->q_remark;
        $this->with_sst = $revision->with_sst;
        $this->show_breakdown = $revision->show_breakdown;
        $this->show_breakdown_price = $revision->show_breakdown_price;
        if ($this->save()) {
            $panels = $revision->projectQPanels;
            foreach ($panels as $key => $panel) {
                $panelTemplate = new ProjectQPanelsTemplate();
                $panelTemplate->setAsTemplate($panel, $this->id);
            }
        }
        return true;
    }

    public function cloneTemplate($revisionTemplateMother, $templateName) {
        $this->revision_description = $templateName;
        $this->currency_id = $revisionTemplateMother->currency_id;
        $this->amount = $revisionTemplateMother->amount;
        $this->q_material_offered = $revisionTemplateMother->q_material_offered;
        $this->q_switchboard_standard = $revisionTemplateMother->q_switchboard_standard;
        $this->q_quotation = $revisionTemplateMother->q_quotation;
        $this->q_delivery_ship_mode = $revisionTemplateMother->q_delivery_ship_mode;
        $this->q_delivery_destination = $revisionTemplateMother->q_delivery_destination;
        $this->q_delivery = $revisionTemplateMother->q_delivery;
        $this->q_validity = $revisionTemplateMother->q_validity;
        $this->q_payment = $revisionTemplateMother->q_payment;
        $this->q_remark = $revisionTemplateMother->q_remark;
        $this->with_sst = $revisionTemplateMother->with_sst;
        $this->show_breakdown = $revisionTemplateMother->show_breakdown;
        $this->show_breakdown_price = $revisionTemplateMother->show_breakdown_price;
        if ($this->save()) {
            $panels = $revisionTemplateMother->projectQPanelsTemplates;
            foreach ($panels as $key => $panelTemplate) {
                $newPanelTemplate = new ProjectQPanelsTemplate();
                $newPanelTemplate->cloneTemplate($panelTemplate, $this->id);
            }
        }
        return true;
    }

    public function processAndDelete() {
        foreach ($this->projectQPanelsTemplates as $panel) {
            $panel->processAndDelete();
        }
        return $this->delete();
    }

    public function updateRevisionAmount() {
        $sum = ProjectQPanelsTemplate::find()->where(['revision_template_id' => $this->id])->sum('amount * quantity');
        $this->amount = $sum;
        return $this->update(false);
    }

    public function processAndUpdate() {
        $this->q_delivery_ship_mode = trim($this->q_delivery_ship_mode) == "" ? null : $this->q_delivery_ship_mode;
        return $this->update(false);
    }
}
