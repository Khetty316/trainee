<?php

namespace frontend\models\ProjectProduction;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "project_production_panel_proc_dispatch_master".
 *
 * @property int $id
 * @property string|null $dispatch_no
 * @property int $proj_prod_panel_id
 * @property string|null $remarks
 * @property string|null $dispatched_at
 * @property int|null $dispatched_by
 * @property string|null $responded_at
 * @property int|null $responded_by
 * @property string|null $status
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelProcDispatchItems[] $projectProductionPanelProcDispatchItems
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProdDispatchStatus $status0
 */
class ProjectProductionPanelProcDispatchMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_proc_dispatch_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id'], 'required'],
            [['proj_prod_panel_id', 'dispatched_by', 'responded_by', 'created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['dispatched_at', 'responded_at', 'created_at', 'updated_at'], 'safe'],
            [['dispatch_no'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 10],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdDispatchStatus::className(), 'targetAttribute' => ['status' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dispatch_no' => 'Dispatch No',
            'proj_prod_panel_id' => 'Panel Code',
            'remarks' => 'Remarks',
            'dispatched_at' => 'Dispatched At',
            'dispatched_by' => 'Dispatched By',
            'responded_at' => 'Responded At',
            'responded_by' => 'Responded By',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjectProductionPanelProcDispatchItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelProcDispatchItems() {
        return $this->hasMany(ProjectProductionPanelProcDispatchItems::className(), ['proc_dispatch_master_id' => 'id']);
    }

    /**
     * Gets query for [[ProjProdPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'proj_prod_panel_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefProdDispatchStatus::className(), ['code' => 'status']);
    }

    public function dispatch() {
        $this->dispatched_at = new Expression("NOW()");
        $this->dispatched_by = Yii::$app->user->id;
        $this->dispatch_no = $this->generateDispatchNo();
        $this->status = RefProdDispatchStatus::STS_Dispatched;
        return $this->save();
    }

    public function saveDispatchItems($post) {
        if (empty($post['itemId'])) {
            return false;
        }
        $hasItem = false;
        $itemIds = $post['itemId'];
        $dispatchQtys = $post['dispatchQty'];
        foreach ((array) $itemIds as $key => $itemId) {
            if (!empty($dispatchQtys[$key])) {
                $item = ProjectProductionPanelItems::find()
                        ->where(['id' => $itemId, 'proj_prod_panel_id' => $this->proj_prod_panel_id])
                        ->one();
                if ($item) {
                    $dispatchItem = new ProjectProductionPanelProcDispatchItems();
                    if ($dispatchItem->processDispatchItem($this, $item, $dispatchQtys[$key])) {
                        $hasItem = true;
                    }
                }
            }
        }
        return $hasItem;
    }

    public function updateReceiveStatus($acceptStatus) {
        $this->responded_by = Yii::$app->user->id;
        $this->responded_at = new Expression('NOW()');
        if ($acceptStatus == 1) {
            $this->status = RefProdDispatchStatus::STS_Receive;
        } else if ($acceptStatus == 0) {
            $this->status = RefProdDispatchStatus::STS_Reject;
        }
        return $this->update();
    }

    // resume the items' balance if a dispatch is rejected
    public function resumeBalance() {
        foreach ($this->projectProductionPanelProcDispatchItems as $dispatchItem) {
            $dispatchItem->restoreBalance();
        }
    }

    private function generateDispatchNo() {
        return "ProcD-" . time();
    }

}
