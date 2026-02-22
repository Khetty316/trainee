<?php

namespace frontend\models\ProjectProduction;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqItems;
use frontend\models\ProjectProduction\RefProjProdBqStatus;

/**
 * This is the model class for table "project_production_panel_fab_bq_master".
 *
 * @property int $id
 * @property string|null $bq_no
 * @property int $proj_prod_panel_id
 * @property string|null $remarks
 * @property string|null $submitted_at
 * @property int|null $submitted_by
 * @property string|null $bq_status
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelFabBqItems[] $projectProductionPanelFabBqItems
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjProdBqStatus $bqStatus
 * @property ProjectProductionPanelStoreDispatchMaster[] $projectProductionPanelStoreDispatchMasters
 */
class ProjectProductionPanelFabBqMaster extends \yii\db\ActiveRecord {

    public $isSubmission;
    public $project_code;
    public $panel_code;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_fab_bq_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id'], 'required'],
            [['proj_prod_panel_id', 'submitted_by', 'created_by', 'updated_by', 'isSubmission'], 'integer'],
            [['remarks'], 'string'],
            [['submitted_at', 'created_at', 'updated_at'], 'safe'],
            [['bq_no'], 'string', 'max' => 255],
            [['bq_status'], 'string', 'max' => 10],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['bq_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdBqStatus::className(), 'targetAttribute' => ['bq_status' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'bq_no' => 'BQ Number',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'remarks' => 'Remarks',
            'submitted_at' => 'Submitted At',
            'submitted_by' => 'Submitted By',
            'bq_status' => 'Bq Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjectProductionPanelFabBqItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelFabBqItems() {
        return $this->hasMany(ProjectProductionPanelFabBqItems::className(), ['fab_bq_master_id' => 'id']);
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
     * Gets query for [[BqStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBqStatus() {
        return $this->hasOne(RefProjProdBqStatus::className(), ['code' => 'bq_status']);
    }

    /**
     * Gets query for [[ProjectProductionPanelStoreDispatchMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelStoreDispatchMasters() {
        return $this->hasMany(ProjectProductionPanelStoreDispatchMaster::className(), ['fab_bq_master_id' => 'id']);
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
        if ($this->isSubmission == 1) {
            $this->updateSubmission();
            $this->bq_status = RefProjProdBqStatus::STS_Submitted;
        } else {
            $this->bq_status = RefProjProdBqStatus::STS_Saved;
        }
        if (empty($this->bq_no)) {
            $this->generateBqNo();
        }
        return $this->save();
    }

    // *************************** GENERAL FUNCTIONS


    public function generateBqNo() {
        $this->bq_no = ($this->proj_prod_panel_id ?? "") . "-BQ-" . time();
        return true;
    }

    public function saveBQItems($post) {
        if (empty($post['itemDescription'])) {
            return true;
        }

        $itemDescriptions = $post['itemDescription'];
        $itemQuantitys = $post['quantity'];
        $itemUnits = $post['unitCode'];
        $itemIds = $post['itemId'];
        foreach ((array) $itemDescriptions as $key => $itemDescription) {
            $itemDescription = trim($itemDescription);
            if (!empty($itemDescription)) {
                if (empty($itemIds[$key])) { // if new
                    $item = new ProjectProductionPanelFabBqItems();
                    $item->item_description = $itemDescription;
                    $item->quantity = $itemQuantitys[$key];
                    $item->balance = $itemQuantitys[$key];
                    $item->unit_code = $itemUnits[$key];
                    $item->fab_bq_master_id = $this->id;
                    if (!$item->save()) {
                        \common\models\myTools\Mydebug::dumpFileA($item->errors);
                    }
                } else { // if old, update
                    $item = ProjectProductionPanelFabBqItems::findOne($itemIds[$key]);
                    $item->item_description = $itemDescription;
                    $item->quantity = $itemQuantitys[$key];
                    $item->balance = $itemQuantitys[$key];
                    $item->unit_code = $itemUnits[$key];
                    $item->update();
                }
            } else {
                // Delete if has id
                if (!empty($itemIds[$key])) {
                    $item = ProjectProductionPanelFabBqItems::findOne($itemIds[$key]);
                    $item->delete();
                }
            }
        }

        return true;
    }

    // To check if the item is dispatched
    // Update status to dispatched or fully dispatched
    public function updateStatus() {
        $items = ProjectProductionPanelFabBqItems::find()->where(['fab_bq_master_id' => $this->id])
                        ->andWhere('balance > 0')->all();
        if (!empty($items)) {
            $this->bq_status = RefProjProdBqStatus::STS_Dispatched;
        } else {
            $this->bq_status = RefProjProdBqStatus::STS_FullyDispatched;
        }
        return $this->update();
    }

    // To check if the BQ is completed
    public function checkIfCompleted() {
        $balance = Yii::$app->db->createCommand("SELECT items.id,items.quantity-IFNULL(received.total_received,0) AS total_received "
                        . "FROM project_production_panel_fab_bq_items AS items "
                        . "LEFT JOIN (SELECT SUM(b.quantity) AS 'total_received', b.fab_bq_item_id "
                        . "FROM project_production_panel_store_dispatch_master AS a "
                        . "JOIN project_production_panel_store_dispatch_items AS b ON a.id = b.store_dispatch_master_id "
                        . "WHERE a.fab_bq_master_id = " . $this->id . " AND a.status = '" . RefProdDispatchStatus::STS_Receive . "' "
                        . "GROUP BY b.fab_bq_item_id) AS received "
                        . "ON items.id = received.fab_bq_item_id "
                        . "WHERE items.fab_bq_master_id = " . $this->id
                        . " HAVING total_received > 0")->queryAll();
        if (empty($balance)) {
            $this->bq_status = RefProjProdBqStatus::STS_Done;
            $this->update();
        }
    }

    private function updateSubmission() {
        $this->submitted_at = new \yii\db\Expression("NOW()");
        $this->submitted_by = Yii::$app->user->id;
    }

}
