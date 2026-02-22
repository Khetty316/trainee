<?php

namespace frontend\models\projectproduction;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\bom\BomMaster;
/**
 * This is the model class for table "v_project_production_panels".
 *
 * @property int $id
 * @property string|null $project_production_panel_code
 * @property int $proj_prod_master
 * @property string|null $panel_description
 * @property int|null $quantity
 * @property string|null $project_type_name
 * @property string|null $unit_code
 * @property string|null $filename
 * @property string|null $finalized_at
 * @property int|null $finalized_by
 * @property string|null $design_completed_at
 * @property string $activeStatus
 */
class VProjectProductionPanels extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_project_production_panels';
    }

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'proj_prod_master', 'quantity', 'finalized_by'], 'integer'],
            [['finalized_at', 'design_completed_at', 'project_type_name'], 'safe'],
            [['amount'],'number'],
            [['project_production_panel_code', 'panel_description', 'filename'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['activeStatus'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_production_panel_code' => 'Project Production Panel Code',
            'proj_prod_master' => 'Proj Prod Master',
            'panel_description' => 'Panel Description',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'filename' => 'Filename',
            'finalized_at' => 'Finalized At',
            'finalized_by' => 'Finalized By',
            'design_completed_at' => 'Design Completed At',
            'activeStatus' => 'Active Status',
            'amount'=>'Amount',
            'project_type_name' => 'Panel Type',
        ];
    }

    /**
     * Gets query for [[ProjProdMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdMaster() {
        return $this->hasOne(ProjectProductionMaster::class, ['id' => 'proj_prod_master']);
    }
    public function getBomMasters() {
        return $this->hasMany(BomMaster::className(), ['production_panel_id' => 'id']);
    }
    
        /**
     * Gets query for [[StockOutboundMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundMasters() {
        return $this->hasMany(\frontend\models\bom\StockOutboundMaster::className(), ['production_panel_id' => 'id']);
    }
}
