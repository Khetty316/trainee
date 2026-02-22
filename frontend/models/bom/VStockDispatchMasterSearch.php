<?php

namespace frontend\models\bom;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\bom\VStockDispatchMaster;

/**
 * VStockDispatchMasterSearch represents the model behind the search form of `frontend\models\bom\VStockDispatchMaster`.
 */
class VStockDispatchMasterSearch extends VStockDispatchMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['dispatch_id', 'production_panel_id', 'dispatch_created_by', 'master_status', 'master_trial_status', 'stock_outbound_details_id', 'stock_outbound_master_id', 'bom_detail_id', 'active_sts', 'fully_dispatch_status'], 'integer'],
            [['dispatch_no', 'dispatch_created_at', 'received_by', 'master_status_updated_at', 'model_type', 'brand', 'descriptions', 'engineer_remark'], 'safe'],
            [['total_trial_dispatch_qty', 'detail_qty', 'dispatched_qty', 'unacknowledged_qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
    public function search($params, $params2 = "") {
        $query = VStockDispatchMaster::find();

        // add conditions that should always apply here

        if ($params2 != "") {
            $query->andFilterWhere(['dispatch_id' => $params2]);
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
            'dispatch_id' => $this->dispatch_id,
            'production_panel_id' => $this->production_panel_id,
            'dispatch_created_at' => $this->dispatch_created_at,
            'dispatch_created_by' => $this->dispatch_created_by,
            'master_status' => $this->master_status,
            'master_status_updated_at' => $this->master_status_updated_at,
            'master_trial_status' => $this->master_trial_status,
            'stock_outbound_details_id' => $this->stock_outbound_details_id,
            'total_trial_dispatch_qty' => $this->total_trial_dispatch_qty,
            'stock_outbound_master_id' => $this->stock_outbound_master_id,
            'bom_detail_id' => $this->bom_detail_id,
            'detail_qty' => $this->detail_qty,
            'dispatched_qty' => $this->dispatched_qty,
            'unacknowledged_qty' => $this->unacknowledged_qty,
            'active_sts' => $this->active_sts,
            'fully_dispatch_status' => $this->fully_dispatch_status,
        ]);

        $query->andFilterWhere(['like', 'dispatch_no', $this->dispatch_no])
                ->andFilterWhere(['like', 'received_by', $this->received_by])
                ->andFilterWhere(['like', 'model_type', $this->model_type])
                ->andFilterWhere(['like', 'brand', $this->brand])
                ->andFilterWhere(['like', 'descriptions', $this->descriptions])
                ->andFilterWhere(['like', 'engineer_remark', $this->engineer_remark]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['dispatch_created_at' => SORT_DESC,]);
        }

        return $dataProvider;
    }
}
