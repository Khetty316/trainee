<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectproduction\ProjectProductionPanelFabBqItems;

/**
 * ProjectProductionPanelFabBqItemsSearch represents the model behind the search form of `frontend\models\projectproduction\ProjectProductionPanelFabBqItems`.
 */
class ProjectProductionPanelFabBqItemsSearch extends ProjectProductionPanelFabBqItems {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'fab_bq_master_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['item_description', 'unit_code', 'created_at', 'updated_at'], 'safe'],
            [['quantity'], 'number'],
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
    public function search($params, $type = "", $myParams = []) {
        $query = ProjectProductionPanelFabBqItems::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        switch ($type) {
            case 'byBq':
                $query->where(['fab_bq_master_id' => $myParams['bqId']]);
                break;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fab_bq_master_id' => $this->fab_bq_master_id,
            'quantity' => $this->quantity,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'item_description', $this->item_description])
                ->andFilterWhere(['like', 'unit_code', $this->unit_code]);

        return $dataProvider;
    }

}
