<?php

namespace frontend\models\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\cmms\CmmsFaultList;
use Yii;

/**
 * CmmsFaultListSearch represents the model behind the search form of `frontend\models\cmms\CmmsFaultList`.
 */
class CmmsFaultListSearch extends CmmsFaultList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_by', 'id', 'reported_by', 'reviewed_by', 'cmms_work_order_id', 'status', 'is_deleted', 'asset_id', 'superior_id', 'active_sts', 'machine_priority_id', 'cmms_asset_list_id'], 'integer'],
            [['code', 'reviewed_at', 'updated_at','reported_at', 'follow_up_required', 'maintenance_type', 'additional_remarks', 'fault_area', 'fault_section', 'fault_asset_id', 'fault_type', 'fault_primary_detail', 'fault_secondary_detail'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $context)
    {
        $query = CmmsFaultList::find();

        // add conditions that should always apply here
        switch ($context) {
            case "personalActive":
                $query->where(['cmms_fault_list.reported_by' => Yii::$app->user->identity->id])
                        ->andWhere(['cmms_fault_list.status' => RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION])
                        ->andWhere(['cmms_fault_list.is_deleted' => 0])
                        ->andWhere(['cmms_fault_list.active_sts' => 1]);
                break;

            case "personalAll":
                $query->where(['cmms_fault_list.reported_by' => Yii::$app->user->identity->id])
                      ->andWhere(['cmms_fault_list.is_deleted' => 0])
                      ->andWhere(['cmms_fault_list.active_sts' => 1]);
                break;
            
//            case "personalReportFault":
//                $query->where(['cmms_fault_list.reported_by' => Yii::$app->user->identity->id])
//                      ->andWhere(['cmms_fault_list.is_deleted' => 0])
//                      ->andWhere(['cmms_fault_list.active_sts' => 1]);
//                break;
            
            case "superiorActive":
                $query->where(['cmms_fault_list.superior_id' => Yii::$app->user->identity->id])
                        ->andWhere(['cmms_fault_list.status' => RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION])
                        ->andWhere(['cmms_fault_list.is_deleted' => 0])
                        ->andWhere(['cmms_fault_list.active_sts' => 1]);
                break;

            case "superiorAll":
                $query->where(['cmms_fault_list.superior_id' => Yii::$app->user->identity->id])
//                      ->andWhere(['cmms_fault_list.status' => RefCmmsStatus::$STATUS_WORK_ORDER_CREATION])
                      ->andWhere(['cmms_fault_list.is_deleted' => 0])
                      ->andWhere(['cmms_fault_list.active_sts' => 1]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'reported_by' => $this->reported_by,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at,
            'cmms_work_order_id' => $this->cmms_work_order_id,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            'reported_at' => $this->reported_at,
            'asset_id' => $this->asset_id,
            'superior_id' => $this->superior_id,
            'active_sts' => $this->active_sts,
            'machine_priority_id' => $this->machine_priority_id,
            'cmms_asset_list_id' => $this->cmms_asset_list_id,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'follow_up_required', $this->follow_up_required])
            ->andFilterWhere(['like', 'maintenance_type', $this->maintenance_type])
            ->andFilterWhere(['like', 'additional_remarks', $this->additional_remarks])
            ->andFilterWhere(['like', 'fault_area', $this->fault_area])
            ->andFilterWhere(['like', 'fault_section', $this->fault_section])
            ->andFilterWhere(['like', 'fault_asset_id', $this->fault_asset_id])
            ->andFilterWhere(['like', 'fault_type', $this->fault_type])
            ->andFilterWhere(['like', 'fault_primary_detail', $this->fault_primary_detail])
            ->andFilterWhere(['like', 'fault_secondary_detail', $this->fault_secondary_detail]);

        return $dataProvider;
    }
}
