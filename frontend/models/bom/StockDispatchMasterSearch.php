<?php

namespace frontend\models\bom;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\bom\StockDispatchMaster;

/**
 * StockDispatchMasterSearch represents the model behind the search form of `frontend\models\bom\StockDispatchMaster`.
 */
class StockDispatchMasterSearch extends StockDispatchMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'production_panel_id', 'created_by', 'received_by', 'status', 'trial_status'], 'integer'],
            [['dispatch_no', 'created_at', 'status_updated_at', 'created_by', 'received_by', ], 'safe'],
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
    public function search($params, $params2 = "", $productionPanelId = "") {
        $query = StockDispatchMaster::find();

        // add conditions that should always apply here
        switch ($params2) {
            case "pending":
                $query->where(['trial_status' => 0, 'received_by' => \Yii::$app->user->id]);
                break;
            case "acknowledged":
                $query->where(['trial_status' => 1, 'received_by' => \Yii::$app->user->id]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user creator", "stock_dispatch_master.created_by = creator.id");
        $query->join("LEFT JOIN", "user receiver", "stock_dispatch_master.received_by = receiver.id");
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'production_panel_id' => $this->production_panel_id,
//            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
//            'received_by' => $this->received_by,
            'stock_dispatch_master.status' => $this->status,
//            'status_updated_at' => $this->status_updated_at,
//            'trial_status' => $this->trial_status,
        ]);

        if ($productionPanelId !== null) {
            $query->andFilterWhere(['production_panel_id' => $productionPanelId]);
        }

        $query->andFilterWhere(['like', 'dispatch_no', $this->dispatch_no])
                ->andFilterWhere(['like', 'creator.fullname', $this->created_by])
                ->andFilterWhere(['like', 'receiver.fullname', $this->received_by])
                ->andFilterWhere(['like', 'stock_dispatch_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'status_updated_at', $this->status_updated_at]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }

        return $dataProvider;
    }
}
