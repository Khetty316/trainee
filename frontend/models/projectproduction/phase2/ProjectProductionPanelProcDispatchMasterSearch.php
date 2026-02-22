<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ProjectProduction\ProjectProductionPanelProcDispatchMaster;

class ProjectProductionPanelProcDispatchMasterSearch extends ProjectProductionPanelProcDispatchMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['dispatch_no', 'remarks', 'dispatched_at', 'responded_at', 'status', 'created_at', 'updated_at',
            'proj_prod_panel_id', 'dispatched_by', 'responded_by', 'created_by', 'updated_by'], 'safe'],
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
    public function search($params, $otherParams = "") {

        $query = ProjectProductionPanelProcDispatchMaster::find();
        $query->join("INNER JOIN", "project_production_panels", "project_production_panels.id=project_production_panel_proc_dispatch_master.proj_prod_panel_id");
        $query->join("LEFT JOIN", "user as dispatcher", "dispatcher.id=project_production_panel_proc_dispatch_master.dispatched_by");
        $query->join("LEFT JOIN", "user as responder", "responder.id=project_production_panel_proc_dispatch_master.responded_by");

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
//            'proj_prod_panel_id' => $this->proj_prod_panel_id,
//            'dispatched_at' => $this->dispatched_at,
//            'dispatched_by' => $this->dispatched_by,
//            'responded_at' => $this->responded_at,
//            'responded_by' => $this->responded_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'dispatch_no', $this->dispatch_no])
                ->andFilterWhere(['like', 'remarks', $this->remarks])
                ->andFilterWhere(['like', 'project_production_panels.project_production_panel_code', $this->proj_prod_panel_id])
                ->andFilterWhere(['like', 'dispatcher.fullname', $this->dispatched_by])
                ->andFilterWhere(['like', 'responder.fullname', $this->responded_by])
                ->andFilterWhere(['like', 'DATE_FORMAT(dispatched_at,"%d/%m/%Y %H:%i")', $this->dispatched_at])
                ->andFilterWhere(['like', 'DATE_FORMAT(responded_at,"%d/%m/%Y %H:%i")', $this->responded_at])
                ->andFilterWhere(['like', 'project_production_panel_proc_dispatch_master.status', $this->status]);


        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        switch ($otherParams) {
            case 'indexToReceiveItem':
                $dataProvider->query->andWhere(['project_production_panel_proc_dispatch_master.status' => RefProdDispatchStatus::STS_Dispatched]);
                break;
        }
        return $dataProvider;
    }

}
