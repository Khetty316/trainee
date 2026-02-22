<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_bq_status".
 *
 * @property string $code
 * @property string $status_name
 * @property int|null $order
 *
 * @property ProjectProductionPanelFabBqMaster[] $projectProductionPanelFabBqMasters
 */
class RefProjProdBqStatus extends \yii\db\ActiveRecord {

    CONST STS_Cancelled = "cancel";
    CONST STS_Dispatched = "dispatch";
    CONST STS_Done = "done";
    CONST STS_FullyDispatched = "fullydis";
    CONST STS_Saved = "saved";
    CONST STS_Submitted = "submit";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_bq_status';
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
     * Gets query for [[ProjectProductionPanelFabBqMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelFabBqMasters() {
        return $this->hasMany(ProjectProductionPanelFabBqMaster::className(), ['bq_status' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProjProdBqStatus::find()->orderBy(['order' => SORT_ASC])->all(), "code", "status_name");
    }

}
