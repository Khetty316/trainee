<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqMaster;
use frontend\models\ProjectProduction\RefProjProdBqStatus;

class ProjectProductionPanelFabBqMasterSearch extends ProjectProductionPanelFabBqMaster {

    public $project_code;
    public $panel_code;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'proj_prod_panel_id', 'created_by', 'updated_by'], 'integer'],
            [['bq_no', 'remarks', 'created_at', 'updated_at', 'bq_status', 'project_code', 'panel_code'], 'safe'],
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

        $query = ProjectProductionPanelFabBqMaster::find();
        $query->join("INNER JOIN", "user", "user.id=project_production_panel_fab_bq_master.created_by");
        $query->join("INNER JOIN", "project_production_panels", "project_production_panels.id=project_production_panel_fab_bq_master.proj_prod_panel_id");
        $query->join("INNER JOIN", "project_production_master", "project_production_master.id=project_production_panels.proj_prod_master");
        $query->select([
            "project_production_panel_fab_bq_master.*",
            "project_production_master.project_production_code as project_code",
            "project_production_panels.project_production_panel_code as panel_code"
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        switch ($type) {
            case 'byPanel':
                $query->where(['proj_prod_panel_id' => $myParams['panelId']]);
                break;
            case 'awaitingDispatch':
                $statusList = [RefProjProdBqStatus::STS_Submitted,
                    RefProjProdBqStatus::STS_Dispatched,
                    RefProjProdBqStatus::STS_FullyDispatched];
                $query->where(['bq_status' => $statusList]);
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
            'proj_prod_panel_id' => $this->proj_prod_panel_id,
            'bq_status' => $this->bq_status,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'bq_no', $this->bq_no])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'DATE_FORMAT(project_production_panel_fab_bq_master.created_at,\'%d/%m/%Y %H:%i\')', $this->created_at])
                ->andFilterWhere(['like', 'project_production_panels.project_production_panel_code', $this->panel_code])
                ->andFilterWhere(['like', 'project_production_master.project_production_code', $this->project_code])
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
        return $dataProvider;
    }

}
