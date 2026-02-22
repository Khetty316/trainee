<?php

namespace frontend\models\bom;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanels;

/**
 * This is the model class for table "bom_master".
 *
 * @property int $id
 * @property int $production_panel_id
 * @property int|null $qty
 * @property int $finalized_status 0 = no, 1 = fully, 2 = partially
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property BomDetails[] $bomDetails
 * @property ProjectProductionPanels $productionPanel
 * @property StockOutboundMaster[] $stockOutboundMasters
 */
class BomMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'bom_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['production_panel_id'], 'required'],
            [['production_panel_id'], 'unique'],
            [['production_panel_id', 'qty', 'finalized_status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['production_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['production_panel_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'production_panel_id' => 'Production Panel ID',
            'qty' => 'Quantity',
            'finalized_status' => 'Finalized Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[BomDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBomDetails() {
        return $this->hasMany(BomDetails::className(), ['bom_master' => 'id']);
    }

    /**
     * Gets query for [[ProductionPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'production_panel_id']);
    }

    /**
     * Gets query for [[StockOutboundMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundMasters() {
        return $this->hasMany(StockOutboundMaster::className(), ['bom_master_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

}
