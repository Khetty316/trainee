<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_panel_work_status".
 *
 * @property string $code
 * @property string $status_name
 * @property int|null $order
 *
 * @property ProjectProductionPanels[] $fabPanels
 * @property ProjectProductionPanels[] $elecPanels
 */
class RefProjProdPanelWorkStatus extends \yii\db\ActiveRecord {

    CONST STS_1_Pending = "pending";
    CONST STS_2_Partial = "partial";
    CONST STS_3_Fully = "fully";
    CONST STS_4_Complete = "completed";
    CONST STS_5_Cancel = "cancel";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_panel_work_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'status_name'], 'required'],
            [['order'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['status_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'status_name' => 'Status Name',
            'order' => 'Order',
        ];
    }

    /**
     * Gets query for [[fabPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabPanels() {
        return $this->hasMany(ProjectProductionPanels::className(), ['fab_work_status' => 'code']);
    }

    /**
     * Gets query for [[elecPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecPanels() {
        return $this->hasMany(ProjectProductionPanels::className(), ['elec_work_status' => 'code']);
    }

}
