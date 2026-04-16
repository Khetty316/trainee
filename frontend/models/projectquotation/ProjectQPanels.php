<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\common\RefProjectQPanelUnit;
use frontend\models\common\RefProjectQTypes;
use frontend\models\ProjectProduction\ProjectProductionPanels;

/**
 * This is the model class for table "project_q_panels".
 *
 * @property int $id
 * @property int $revision_id
 * @property string|null $panel_description
 * @property string|null $remark
 * @property float|null $amount
 * @property int $sort
 * @property float $quantity
 * @property string|null $unit_code
 * @property int|null $by_item_price
 * @property string|null $panel_type
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefProjectQTypes $panelType
 * @property ProjectProductionPanels[] $projectProductionPanels
 * @property ProjectQPanelItems[] $projectQPanelItems
 * @property ProjectQRevisions $revision
 * @property RefProjectQPanelUnit $unitCode
 * @property WorkAssignmentMaster[] $workAssignmentMasters
 */
class ProjectQPanels extends \yii\db\ActiveRecord {

    CONST Prefix = "PAN";
    CONST runningNoLength = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_panels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['revision_id','panel_description','quantity','unit_code','panel_type'],'required'],
            [['remark'], 'safe'],
            [['revision_id', 'sort', 'by_item_price', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['amount', 'quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['panel_description'], 'string', 'max' => 255],
            [['unit_code', 'panel_type'], 'string', 'max' => 10],
            [['revision_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQRevisions::className(), 'targetAttribute' => ['revision_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQPanelUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::class, 'targetAttribute' => ['panel_type' => 'code']]
    ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'revision_id' => 'Revision ID',
            'panel_description' => 'Panel Name',
            'remark' => 'Remark',
            'amount' => 'Unit Price',
            'sort' => 'Sort',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit',
            'by_item_price' => 'By Item Price',
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
     * Gets query for [[ProjectProductionPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels() {
        return $this->hasMany(ProjectProductionPanels::className(), ['panel_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectQPanelItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQPanelItems() {
        return $this->hasMany(ProjectQPanelItems::className(), ['panel_id' => 'id']);
    }

    /**
     * Gets query for [[Revision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevision() {
        return $this->hasOne(ProjectQRevisions::className(), ['id' => 'revision_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectQPanelUnit::className(), ['code' => 'unit_code']);
    }

    /**
     * Gets query for [[WorkAssignmentMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkAssignmentMasters() {
        return $this->hasMany(WorkAssignmentMaster::className(), ['project_q_panel_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

//        if ($this->panel_type = "") {
//            $this->panel_type = new \yii\db\Expression("null");
//        }

        return parent::beforeSave($insert);
    }

    public function cloneFromMother($motherPanelId, $clonePanelNewName, $revisionId = '') {
        $motherPanel = ProjectQPanels::findOne($motherPanelId);
        $this->revision_id = $revisionId == "" ? $motherPanel->revision_id : $revisionId;
        $this->panel_description = $clonePanelNewName;
        $this->remark = $motherPanel->remark;
        $this->amount = $motherPanel->amount;
        $this->quantity = $motherPanel->quantity;
        $this->unit_code = $motherPanel->unit_code;
//        $this->sort = (ProjectQPanels::find()->where(['revision_id' => $motherPanel->revision_id])->max('sort')) + 1;
        $this->sort = $motherPanel->sort;
        $this->panel_type = $motherPanel->panel_type ?? $this->revision->projectQType->type;
        $this->by_item_price = $motherPanel->by_item_price;
        if ($this->save()) {
            $items = $motherPanel->projectQPanelItems;
            foreach ($items as $key => $item) {
                $item->clonePanelItemFromMother($this->id);
            }
        }
        return true;
    }

    public function processAndSave() {

//        $this->amount = 0;  // disable temporarily before need to add item in panel 11/12/2021
        $this->sort = (ProjectQPanels::find()->where(['revision_id' => $this->revision_id])->max('sort')) + 1;
        return $this->save();
    }

    public function getPanelAmountFromItems() {
        $totalAmount = ProjectQPanelItems::find()->where(['panel_id' => $this->id])->sum('amount * quantity');
        return $totalAmount ?? 0;
    }

    public function updatePanelAmount() {
        $this->amount = $this->getPanelAmountFromItems();
        return $this->update();
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update();
    }

    public function createFromTemplate($panelTemplate, $revisionId) {
        $this->revision_id = $revisionId;
        $this->panel_description = $panelTemplate->panel_description;
        $this->remark = $panelTemplate->remark;
        $this->amount = $panelTemplate->amount;
        $this->sort = $panelTemplate->sort;
        $this->quantity = $panelTemplate->quantity;
        $this->panel_type = $panelTemplate->panel_type ?? $this->revision->projectQType->type;
        $this->unit_code = $panelTemplate->unit_code;
        if ($this->save(true)) {
            $itemTemplates = $panelTemplate->projectQPanelItemsTemplates;
            foreach ($itemTemplates as $key => $itemTemplate) {
                $newItems = new ProjectQPanelItems();
                $newItems->createFromTemplate($itemTemplate, $this->id);
            }
        } else {
            \common\models\myTools\Mydebug::dumpFileA($this->errors);
        }
    }

    public static function getPanelsFromActiveRevision($projectQId) {
        return ProjectQPanels::find()
                        ->innerJoin("project_q_revisions", "project_q_panels.`revision_id`=project_q_revisions.`id`")
                        ->innerJoin("project_q_types", "project_q_revisions.`project_q_type_id`=project_q_types.`id`")
                        ->where(['project_q_types.project_id' => $projectQId])
                        ->all();
    }

    public static function getPanelsFromActiveRevisionAutocompleteList($projectQId, $term) {
        return ProjectQPanels::find()->select(['panel_description as value', 'project_q_panels.id as id', 'panel_description as label', 'quantity'])
                        ->innerJoin("project_q_revisions", "project_q_panels.`revision_id`=project_q_revisions.`id`")
                        ->innerJoin("project_q_types", "project_q_revisions.`id`=project_q_types.active_revision_id")
                        ->where(['project_q_types.project_id' => $projectQId])
                        ->andWhere("panel_description LIKE '%" . addslashes($term) . "%'")
                        ->orderBy(['panel_description' => SORT_ASC])
                        ->asArray()
                        ->all();
    }

}
