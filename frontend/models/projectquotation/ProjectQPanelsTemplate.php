<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\projectquotation\ProjectQPanelItemsTemplate;
use frontend\models\common\RefProjectQPanelUnit;
use frontend\models\common\RefProjectQTypes;

/**
 * This is the model class for table "project_q_panels_template".
 *
 * @property int $id
 * @property int $revision_template_id
 * @property string|null $panel_description
 * @property string|null $remark
 * @property float|null $amount
 * @property int $sort
 * @property float $quantity
 * @property string|null $unit_code
 * @property string|null $panel_type
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefProjectQTypes $panelType
 * @property ProjectQPanelItemsTemplate[] $projectQPanelItemsTemplates
 * @property ProjectQRevisionsTemplate $revisionTemplate
 * @property RefProjectQPanelUnit $unitCode
 */
class ProjectQPanelsTemplate extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_panels_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['revision_template_id', 'panel_type'], 'required'],
            [['revision_template_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['amount', 'quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['panel_description'], 'string', 'max' => 255],
            [['unit_code', 'panel_type'], 'string', 'max' => 10],
            [['revision_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQRevisionsTemplate::className(), 'targetAttribute' => ['revision_template_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQPanelUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::class, 'targetAttribute' => ['panel_type' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'revision_template_id' => 'Revision Template ID',
            'panel_description' => 'Panel Description',
            'remark' => 'Remark',
            'amount' => 'Amount',
            'sort' => 'Sort',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'panel_type' => 'Panel Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[PanelType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanelType() {
        return $this->hasOne(RefProjectQTypes::class, ['code' => 'panel_type']);
    }

    /**
     * Gets query for [[ProjectQPanelItemsTemplates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQPanelItemsTemplates() {
        return $this->hasMany(ProjectQPanelItemsTemplate::className(), ['panel_template_id' => 'id']);
    }

    /**
     * Gets query for [[RevisionTemplate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionTemplate() {
        return $this->hasOne(ProjectQRevisionsTemplate::className(), ['id' => 'revision_template_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectQPanelUnit::className(), ['code' => 'unit_code']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        if ($this->panel_type == '') {
            $this->panel_type = null;
        }

        return parent::beforeSave($insert);
    }

    public function setAsTemplate($panel, $revisionTemplateId) {
        $this->revision_template_id = $revisionTemplateId;
        $this->panel_description = $panel->panel_description;
        $this->remark = $panel->remark;
        $this->amount = $panel->amount;
        $this->sort = $panel->sort;
        $this->quantity = $panel->quantity;
        $this->unit_code = $panel->unit_code;
        $this->panel_type = $panel->panel_type;
        if ($this->save(false)) {
            $items = $panel->projectQPanelItems;
            foreach ($items as $key => $item) {
                $itemTemplate = new ProjectQPanelItemsTemplate();
                $itemTemplate->setAsTemplate($item, $this->id);
            }
        }
        return true;
    }

    public function cloneTemplate($panelTemplate, $revisionTemplateId) {
        $this->revision_template_id = $revisionTemplateId;
        $this->panel_description = $panelTemplate->panel_description;
        $this->remark = $panelTemplate->remark;
        $this->amount = $panelTemplate->amount;
        $this->sort = $panelTemplate->sort;
        $this->quantity = $panelTemplate->quantity;
        $this->unit_code = $panelTemplate->unit_code;
        $this->panel_type = $panelTemplate->panel_type;

        if ($this->save(false)) {
            $items = $panelTemplate->projectQPanelItemsTemplates;
            foreach ($items as $key => $itemTemplate) {
                $newItemTemplate = new ProjectQPanelItemsTemplate();
                $newItemTemplate->cloneTemplate($itemTemplate, $this->id);
            }
        }
        return true;
    }

    public function processAndDelete() {
        foreach ($this->projectQPanelItemsTemplates as $item) {
            $item->delete();
        }
        $this->delete();
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update();
    }

    public function processAndSave() {
        $this->sort = (ProjectQPanelsTemplate::find()->where(['revision_template_id' => $this->revision_template_id])->max('sort')) + 1;
        return $this->save();
    }

    public function cloneFromMother($motherPanelTemplateId, $clonePanelNewName, $revisionTemplateId = '') {
        $motherPanel = ProjectQPanelsTemplate::findOne($motherPanelTemplateId);
        $this->revision_template_id = ($revisionTemplateId == "" ? $motherPanel->revision_template_id : $revisionTemplateId);
        $this->panel_description = $clonePanelNewName;
        $this->remark = $motherPanel->remark;
        $this->amount = $motherPanel->amount;
        $this->quantity = $motherPanel->quantity;
        $this->unit_code = $motherPanel->unit_code;
        $this->panel_type = $motherPanel->panel_type;
        $this->sort = (ProjectQPanelsTemplate::find()->where(['revision_template_id' => $motherPanel->revision_template_id])->max('sort')) + 1;
        if ($this->save(false)) {
            $items = $motherPanel->projectQPanelItemsTemplates;
            foreach ($items as $key => $item) {
                $item->clonePanelItemFromMother($this->id);
            }
        } else {
            \common\models\myTools\Mydebug::byFile("Fail.............");
            \common\models\myTools\Mydebug::dumpFileA($this->errors);
        }
        return true;
    }

    public function updatePanelTemplateAmount() {
        $totalAmount = ProjectQPanelItemsTemplate::find()->where(['panel_template_id' => $this->id])->sum('amount');
        $this->amount = $totalAmount;
        return $this->update();
    }

}
