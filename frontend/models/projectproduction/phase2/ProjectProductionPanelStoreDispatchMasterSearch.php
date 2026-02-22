<?php

namespace frontend\models\ProjectProduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMaster;

/**
 * ProjectProductionPanelStoreDispatchMasterSearch represents the model behind the search form of `frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMaster`.
 */
class ProjectProductionPanelStoreDispatchMasterSearch extends ProjectProductionPanelStoreDispatchMaster {

    public $project_code, $project_name, $panel_code, $panel_description, $bq_no;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'fab_bq_master_id'], 'integer'],
            [['dispatch_no', 'remarks', 'dispatched_at', 'responded_at', 'created_at', 'updated_at', 'dispatched_by', 'responded_by', 'created_by', 'updated_by', 'status'], 'safe'],
            [['project_code', 'project_name', 'panel_code', 'panel_description', 'bq_no'], 'safe']
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
        $query = ProjectProductionPanelStoreDispatchMaster::find();


//        $query->join("INNER JOIN", "user", "user.id=project_production_panel_fab_bq_master.created_by");
        $query->join("INNER JOIN", "project_production_panel_fab_bq_master", "project_production_panel_fab_bq_master.id=project_production_panel_store_dispatch_master.fab_bq_master_id");
        $query->join("INNER JOIN", "project_production_panels", "project_production_panels.id=project_production_panel_fab_bq_master.proj_prod_panel_id");
        $query->join("INNER JOIN", "project_production_master", "project_production_master.id=project_production_panels.proj_prod_master");
        $query->select([
            "project_production_panel_store_dispatch_master.*",
            "project_production_panel_fab_bq_master.bq_no",
            "project_production_master.project_production_code as project_code",
            "project_production_master.name as project_name",
            "project_production_panels.project_production_panel_code as panel_code",
            "project_production_panels.panel_description as panel_description"
        ]);

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
            'fab_bq_master_id' => $this->fab_bq_master_id,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'dispatch_no', $this->dispatch_no])
                ->andFilterWhere(['like', 'project_production_panel_fab_bq_master.bq_no', $this->bq_no])
                ->andFilterWhere(['like', 'DATE_FORMAT(dispatched_at.created_at,"%d/%m/%Y %H:%i")', $this->dispatched_at])
                ->andFilterWhere(['like', 'dispatched_by', $this->dispatched_by])
                ->andFilterWhere(['like', 'DATE_FORMAT(responded_at.created_at,"%d/%m/%Y %H:%i")', $this->responded_at])
                ->andFilterWhere(['like', 'responded_by', $this->responded_by])
                ->andFilterWhere(['like', 'project_production_master.name', $this->project_name])
                ->andFilterWhere(['like', 'project_production_panels.project_production_panel_code', $this->panel_code])
                ->andFilterWhere(['like', 'project_production_panels.panel_description as panel_description', $this->panel_description])
                ->andFilterWhere(['like', 'remarks', $this->remarks]);

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        $dataProvider->sort->attributes['project_code'] = [
            'asc' => ['project_production_master.project_production_code' => SORT_ASC],
            'desc' => ['project_production_master.project_production_code' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['panel_code'] = [
            'asc' => ['project_production_panels.project_production_panel_code' => SORT_ASC],
            'desc' => ['project_production_panels.project_production_panel_code' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['bq_no'] = [
            'asc' => ['project_production_panel_fab_bq_master.bq_no' => SORT_ASC],
            'desc' => ['project_production_panel_fab_bq_master.bq_no' => SORT_DESC],
        ];
        return $dataProvider;
    }

}
