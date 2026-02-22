<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "ref_prod_dispatch_status".
 *
 * @property string $code
 * @property string $status_name
 * @property int|null $order
 *
 * @property ProjectProductionPanelStoreDispatchMaster[] $projectProductionPanelStoreDispatchMasters
 */
class RefProdDispatchStatus extends \yii\db\ActiveRecord {

    CONST STS_Cancelled = "cancel";
    CONST STS_Dispatched = "dispatch";
    CONST STS_Receive = "receive";
    CONST STS_Reject = "reject";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_prod_dispatch_status';
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
     * Gets query for [[ProjectProductionPanelStoreDispatchMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelStoreDispatchMasters() {
        return $this->hasMany(ProjectProductionPanelStoreDispatchMaster::className(), ['status' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProdDispatchStatus::find()->orderBy(['order' => SORT_ASC])->all(), "code", "status_name");
    }

}
