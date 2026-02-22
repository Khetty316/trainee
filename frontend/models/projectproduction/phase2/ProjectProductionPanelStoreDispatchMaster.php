<?php

namespace frontend\models\ProjectProduction;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqItems;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchItems;
use yii\db\Expression;
use frontend\models\ProjectProduction\RefProdDispatchStatus;

/**
 * This is the model class for table "project_production_panel_store_dispatch_master".
 *
 * @property int $id
 * @property string|null $dispatch_no
 * @property int $fab_bq_master_id
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
 * @property ProjectProductionPanelStoreDispatchItems[] $projectProductionPanelStoreDispatchItems
 * @property ProjectProductionPanelFabBqMaster $fabBqMaster
 * @property RefProdDispatchStatus $status0
 */
class ProjectProductionPanelStoreDispatchMaster extends \yii\db\ActiveRecord {

    public $project_code, $project_name, $panel_code, $panel_description, $bq_no; // For search model

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_store_dispatch_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['fab_bq_master_id'], 'required'],
            [['fab_bq_master_id', 'dispatched_by', 'responded_by', 'created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['dispatched_at', 'responded_at', 'created_at', 'updated_at'], 'safe'],
            [['dispatch_no'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 10],
            [['fab_bq_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelFabBqMaster::className(), 'targetAttribute' => ['fab_bq_master_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdDispatchStatus::className(), 'targetAttribute' => ['status' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dispatch_no' => 'Dispatch No.',
            'fab_bq_master_id' => 'Fab Bq Master ID',
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

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[ProjectProductionPanelStoreDispatchItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelStoreDispatchItems() {
        return $this->hasMany(ProjectProductionPanelStoreDispatchItems::className(), ['store_dispatch_master_id' => 'id']);
    }

    /**
     * Gets query for [[FabBqMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabBqMaster() {
        return $this->hasOne(ProjectProductionPanelFabBqMaster::className(), ['id' => 'fab_bq_master_id']);
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
                $bqItem = ProjectProductionPanelFabBqItems::find()
                        ->where(['id' => $itemId, 'fab_bq_master_id' => $this->fab_bq_master_id])
                        ->one();
                if ($bqItem) {
                    $dispatchItem = new ProjectProductionPanelStoreDispatchItems();
                    if ($dispatchItem->processDispatchItem($this, $bqItem, $dispatchQtys[$key])) {
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
        foreach ($this->projectProductionPanelStoreDispatchItems as $dispatchItem) {
            $dispatchItem->restoreBalance();
        }
    }

    private function generateDispatchNo() {
        return "SD-" . time();
    }

}
