<?php

namespace frontend\models\bom;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\bom\StockOutboundMaster;

/**
 * StockOutboundMasterSearch represents the model behind the search form of `frontend\models\bom\StockOutboundMaster`.
 */
class StockOutboundMasterSearch extends StockOutboundMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'production_panel_id', 'bom_master_id', 'order', 'fully_dispatched_status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
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
    public function search($params) {
        $query = StockOutboundMaster::find();

        // add conditions that should always apply here

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
            'production_panel_id' => $this->production_panel_id,
            'bom_master_id' => $this->bom_master_id,
            'order' => $this->order,
            'fully_dispatched_status' => $this->fully_dispatched_status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        return $dataProvider;
    }

}
