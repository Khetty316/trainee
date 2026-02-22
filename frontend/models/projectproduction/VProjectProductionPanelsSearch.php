<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectproduction\VProjectProductionPanels;

/**
 * VProjectProductionPanelsSearch represents the model behind the search form of `frontend\models\projectproduction\VProjectProductionPanels`.
 */
class VProjectProductionPanelsSearch extends VProjectProductionPanels {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'proj_prod_master', 'sort', 'quantity', 'finalized_by'], 'integer'],
            [['project_production_panel_code', 'panel_description', 'unit_code', 'filename', 'finalized_at', 'design_completed_at', 'activeStatus'], 'safe'],
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
        $query = VProjectProductionPanels::find();

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
            'proj_prod_master' => $this->proj_prod_master,
            'sort' => $this->sort,
            'quantity' => $this->quantity,
            'finalized_at' => $this->finalized_at,
            'finalized_by' => $this->finalized_by,
            'design_completed_at' => $this->design_completed_at,
        ]);

        $query->andFilterWhere(['like', 'project_production_panel_code', $this->project_production_panel_code])
                ->andFilterWhere(['like', 'panel_description', $this->panel_description])
                ->andFilterWhere(['like', 'unit_code', $this->unit_code])
                ->andFilterWhere(['like', 'filename', $this->filename])
                ->andFilterWhere(['like', 'activeStatus', $this->activeStatus]);

        return $dataProvider;
    }

}
