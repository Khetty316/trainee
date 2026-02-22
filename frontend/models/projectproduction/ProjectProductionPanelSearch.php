<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectproduction\ProjectProductionPanels;
use frontend\models\ProjectProduction\RefProjProdBqStatus;

/**
 * ProjectProductionPanelSearch represents the model behind the search form of `frontend\models\projectproduction\ProjectProductionPanels`.
 */
class ProjectProductionPanelSearch extends ProjectProductionPanels {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'proj_prod_master', 'panel_id', 'sort', 'quantity', 'finalized_by', 'design_completed_by', 'material_completed_by', 'fab_completed_by', 'fab_dispatch_wire_quantity', 'wire_completed_by', 'created_by', 'updated_by'], 'integer'],
            [['project_production_panel_code', 'proj_prod_master', 'panel_description', 'remark', 'unit_code', 'finalized_at', 'item_dispatch_status', 'design_completed_at', 'material_completed_at', 'fab_completed_at', 'wire_completed_at', 'created_at', 'updated_at'], 'safe'],
            [['amount', 'fab_complete_percent', 'wire_complete_percent'], 'number'],
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
    public function search($params, $otherParams = "", $paramArray = []) {
        $query = ProjectProductionPanels::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        proj_prod_master

        $query->innerJoin("project_production_master", "project_production_panels.proj_prod_master = project_production_master.id");
        switch ($otherParams) {
            case 'pendingOrderList':
                $statusArrs = [RefProjProdBqStatus::STS_Submitted,
                    RefProjProdBqStatus::STS_Dispatched,
                    RefProjProdBqStatus::STS_FullyDispatched];
                $query->where(["item_dispatch_status" => $statusArrs]);
                break;
            case 'byProject':
                $query->where(["proj_prod_master" => $paramArray["id"]]);
                if(!empty($paramArray["task_list"])){
                    
                }
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
//            'proj_prod_master' => $this->proj_prod_master,
            'panel_id' => $this->panel_id,
            'amount' => $this->amount,
            'sort' => $this->sort,
//            'quantity' => $this->quantity,
            'finalized_at' => $this->finalized_at,
            'finalized_by' => $this->finalized_by,
            'design_completed_at' => $this->design_completed_at,
            'design_completed_by' => $this->design_completed_by,
            'material_completed_at' => $this->material_completed_at,
            'material_completed_by' => $this->material_completed_by,
            'fab_complete_percent' => $this->fab_complete_percent,
            'fab_completed_at' => $this->fab_completed_at,
            'fab_completed_by' => $this->fab_completed_by,
            'fab_dispatch_wire_quantity' => $this->fab_dispatch_wire_quantity,
            'wire_complete_percent' => $this->wire_complete_percent,
            'wire_completed_at' => $this->wire_completed_at,
            'wire_completed_by' => $this->wire_completed_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'project_production_panel_code', $this->project_production_panel_code])
                ->andFilterWhere(['like', 'panel_description', $this->panel_description])
                ->andFilterWhere(['like', 'quantity', $this->quantity])
                ->andFilterWhere(['like', 'project_production_master.project_production_code', $this->proj_prod_master])
                ->andFilterWhere(['like', 'remark', $this->remark])
                ->andFilterWhere(['like', 'unit_code', $this->unit_code])
                ->andFilterWhere(['like', 'item_dispatch_status', $this->item_dispatch_status]);

//        $dataProvider->setSort([
//            'defaultOrder' => ['id' => SORT_DESC],
//        ]);

        return $dataProvider;
    }

}
