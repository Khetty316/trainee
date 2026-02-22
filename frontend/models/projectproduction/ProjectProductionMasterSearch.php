<?php

namespace frontend\models\ProjectProduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ProjectProduction\ProjectProductionMaster;

/**
 * ProjectProductionMasterSearch represents the model behind the search form of `frontend\models\ProjectProduction\ProjectProductionMaster`.
 */
class ProjectProductionMasterSearch extends ProjectProductionMaster {

    public $component_percentage;
    public $production_fab_complete_percent;
    public $production_elec_complete_percent;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'quotation_id', 'client_id', 'updated_by'], 'integer'],
            [['current_target_date', 'production_fab_complete_percent', 'production_elec_complete_percent', 'component_percentage', 'name', 'project_production_code', 'remark', 'created_at', 'updated_at', 'fab_complete_percent', 'elec_complete_percent', 'clientName', 'created_by'], 'safe'],
        ];
    }

    public function getClientName() {
        if ($this->client_id) {
            $client = \frontend\models\client\Clients::findOne($this->client_id);
            return $client ? $client->company_name : '';
        }
        return '';
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
//    public function search($params, $type = "") {
//        $query = ProjectProductionMaster::find();
//
//        // add conditions that should always apply here
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//
////        switch ($type) {
////            case "indexAwaitingDesign":
////                $query->select('project_production_master.*,(SELECT 1 FROM project_production_panel_design WHERE proj_prod_panel_id = project_production_master.id) AS hasDesign');
////                $query->having('hasDesign IS NULL');
////                break;
////        }
//        $query->select(['project_production_master.*', 'clients.company_name as clientName']);
//        $query->join("LEFT JOIN", "clients", "client_id=clients.id");
//        $query->join("LEFT JOIN", "user", "project_production_master.created_by=user.id");
//        $this->load($params);
//
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        // grid filtering conditions
//        $query->andFilterWhere([
//            'project_production_master.id' => $this->id,
//            'project_production_master.quotation_id' => $this->quotation_id,
//            'project_production_master.client_id' => $this->client_id,
////            'project_production_master.created_at' => $this->created_at,
////            'project_production_master.updated_by' => $this->updated_by,
////            'project_production_master.updated_at' => $this->updated_at,
//        ]);
//
//        $query->andFilterWhere(['like', 'project_production_master.name', $this->name])
//                ->andFilterWhere(['like', 'project_production_master.project_production_code', $this->project_production_code])
//                ->andFilterWhere(['like', 'project_production_master.fab_complete_percent', $this->fab_complete_percent])
//                ->andFilterWhere(['like', 'project_production_master.elec_complete_percent', $this->elec_complete_percent])
//                ->andFilterWhere(['like', 'clients.company_name', $this->clientName])
//                ->andFilterWhere(['like', 'project_production_master.remark', $this->remark])
//                ->andFilterWhere(['like', 'user.fullname', $this->created_by]);
//
//        $dataProvider->setSort([
//            'defaultOrder' => [
//                'id' => SORT_DESC
//            ],
//        ]);
//        $dataProvider->sort->attributes['client_id'] = [
//            'asc' => ['clients.company_name' => SORT_ASC], // use the actual table and column name
//            'desc' => ['clients.company_name' => SORT_DESC],
//        ];
//        return $dataProvider;
//    }
    //before check for the percentage 
//    public function search($params, $type = "") {
//        $query = ProjectProductionMaster::find();
//
//        // Add conditions that should always apply here
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//        $query->select([
//            'project_production_master.*',
//            'clients.company_name AS clientName',
//            'CASE 
//        WHEN (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
//              THEN quantity ELSE 0 END) 
//              FROM project_production_panels 
//              WHERE proj_prod_master = project_production_master.id) > 0 
//        THEN (project_production_master.fab_complete_percent * 
//              (SELECT SUM(quantity) FROM project_production_panels 
//               WHERE proj_prod_master = project_production_master.id)) / 
//             (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
//              THEN quantity ELSE 0 END) 
//              FROM project_production_panels 
//              WHERE proj_prod_master = project_production_master.id)
//        ELSE project_production_master.fab_complete_percent
//    END AS production_fab_complete_percent',
//            'CASE 
//        WHEN (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
//              THEN quantity ELSE 0 END) 
//              FROM project_production_panels 
//              WHERE proj_prod_master = project_production_master.id) > 0 
//        THEN (project_production_master.elec_complete_percent * 
//              (SELECT SUM(quantity) FROM project_production_panels 
//               WHERE proj_prod_master = project_production_master.id)) / 
//             (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
//              THEN quantity ELSE 0 END) 
//              FROM project_production_panels 
//              WHERE proj_prod_master = project_production_master.id)
//        ELSE project_production_master.elec_complete_percent
//    END AS production_elec_complete_percent',
//            'ROUND(
//        (SUM(stock_outbound_details.dispatched_qty) * 100.0) / NULLIF(SUM(stock_outbound_details.qty), 0), 
//        2
//    ) AS component_percentage'
//        ]);
//
////        $query->select([
////            'project_production_master.*',
////            'clients.company_name AS clientName',
////            'ROUND(
////            (SUM(stock_outbound_details.dispatched_qty) * 100.0) / NULLIF(SUM(stock_outbound_details.qty), 0), 
////            2
////        ) AS component_percentage'
////        ]);
//        // Join related tables
//        $query->join("LEFT JOIN", "project_production_panels", "project_production_master.id = project_production_panels.proj_prod_master");
//        $query->join("LEFT JOIN", "stock_outbound_master", "project_production_panels.id = stock_outbound_master.production_panel_id");
//        $query->join("LEFT JOIN", "stock_outbound_details", "stock_outbound_master.id = stock_outbound_details.stock_outbound_master_id");
//        $query->join("LEFT JOIN", "clients", "project_production_master.client_id = clients.id");
//        $query->join("LEFT JOIN", "user", "project_production_master.created_by = user.id");
//
//        $query->groupBy('project_production_master.id');
//
//        $this->load($params);
//
//        if (!$this->validate()) {
//            // Uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        // Grid filtering conditions
//        $query->andFilterWhere([
//            'project_production_master.id' => $this->id,
//            'project_production_master.quotation_id' => $this->quotation_id,
//            'project_production_master.client_id' => $this->client_id,
//            'project_production_master.current_target_date' => $this->current_target_date,
//        ]);
//
//        $query->andFilterWhere(['like', 'project_production_master.name', $this->name])
//                ->andFilterWhere(['like', 'project_production_master.project_production_code', $this->project_production_code])
//                ->andFilterWhere(['like', 'project_production_master.fab_complete_percent', $this->fab_complete_percent])
//                ->andFilterWhere(['like', 'project_production_master.elec_complete_percent', $this->elec_complete_percent])
//                ->andFilterWhere(['like', 'clients.company_name', $this->clientName])
//                ->andFilterWhere(['like', 'project_production_master.remark', $this->remark])
//                ->andFilterWhere(['like', 'project_production_master.created_at', $this->created_at])
//                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
//                ->andFilterHaving(['like', 'production_fab_complete_percent', $this->production_fab_complete_percent])
//                ->andFilterHaving(['like', 'production_elec_complete_percent', $this->production_elec_complete_percent])
////        ->andFilterHaving(['>=', 'ROUND((SUM(stock_outbound_details.dispatched_qty) * 100.0) / NULLIF(SUM(stock_outbound_details.qty), 0), 2)', $this->component_percentage]);
//                ->andFilterHaving(['like', 'component_percentage', $this->component_percentage]);
//
//        $today = new \DateTime();
//        $todayStr = $today->format('Y-m-d');
//
//        if ($type === 'overdue') {
//            $query->andWhere(new \yii\db\Expression(
//                                    "DATEDIFF(project_production_master.current_target_date, :today) < 0"
//                    ))->addParams([':today' => $todayStr])
//                    ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
//                    ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
//                    ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id]);
//        }
//
//        if ($type === 'neardue') {
//            $query->andWhere(new \yii\db\Expression(
//                                    "DATEDIFF(project_production_master.current_target_date, :today) BETWEEN 0 AND 5"
//                    ))->addParams([':today' => $todayStr])
//                    ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
//                    ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
//                    ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id]);
//        }
//
//        $dataProvider->setSort([
//            'defaultOrder' => [
//                'id' => SORT_DESC
//            ],
//        ]);
//        $dataProvider->sort->attributes['client_id'] = [
//            'asc' => ['clients.company_name' => SORT_ASC],
//            'desc' => ['clients.company_name' => SORT_DESC],
//        ];
//        return $dataProvider;
//    }
    public function search($params, $type = "") {
        $query = ProjectProductionMaster::find();

        // Add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->select([
            'project_production_master.*',
            'clients.company_name AS clientName',
            'CASE 
        WHEN (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
              THEN quantity ELSE 0 END) 
              FROM project_production_panels 
              WHERE proj_prod_master = project_production_master.id) > 0 
        THEN (project_production_master.fab_complete_percent * 
              (SELECT SUM(quantity) FROM project_production_panels 
               WHERE proj_prod_master = project_production_master.id)) / 
             (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
              THEN quantity ELSE 0 END) 
              FROM project_production_panels 
              WHERE proj_prod_master = project_production_master.id)
        ELSE project_production_master.fab_complete_percent
    END AS production_fab_complete_percent',
            'CASE 
        WHEN (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
              THEN quantity ELSE 0 END) 
              FROM project_production_panels 
              WHERE proj_prod_master = project_production_master.id) > 0 
        THEN (project_production_master.elec_complete_percent * 
              (SELECT SUM(quantity) FROM project_production_panels 
               WHERE proj_prod_master = project_production_master.id)) / 
             (SELECT SUM(CASE WHEN finalized_at IS NOT NULL AND finalized_by IS NOT NULL 
              THEN quantity ELSE 0 END) 
              FROM project_production_panels 
              WHERE proj_prod_master = project_production_master.id)
        ELSE project_production_master.elec_complete_percent
    END AS production_elec_complete_percent',
            'ROUND(
        (SUM(stock_outbound_details.dispatched_qty) * 100.0) / NULLIF(SUM(stock_outbound_details.qty), 0), 
        2
    ) AS component_percentage',
            // Check if ANY panel has weight
            '(SELECT COUNT(*) 
      FROM project_production_panels ppp
      INNER JOIN prod_fab_task_weight pftw ON pftw.proj_prod_panel_id = ppp.id
      WHERE ppp.proj_prod_master = project_production_master.id 
      AND pftw.panel_type_weight > 0) AS has_fab_tasks',
            // Check if ANY panel has weight  
            '(SELECT COUNT(*) 
      FROM project_production_panels ppp
      INNER JOIN prod_elec_task_weight petw ON petw.proj_prod_panel_id = ppp.id
      WHERE ppp.proj_prod_master = project_production_master.id 
      AND petw.panel_type_weight > 0) AS has_elec_tasks'
        ]);

        // Join related tables
        $query->join("LEFT JOIN", "project_production_panels", "project_production_master.id = project_production_panels.proj_prod_master");
        $query->join("LEFT JOIN", "stock_outbound_master", "project_production_panels.id = stock_outbound_master.production_panel_id");
        $query->join("LEFT JOIN", "stock_outbound_details", "stock_outbound_master.id = stock_outbound_details.stock_outbound_master_id");
        $query->join("LEFT JOIN", "clients", "project_production_master.client_id = clients.id");
        $query->join("LEFT JOIN", "user", "project_production_master.created_by = user.id");

        $query->groupBy('project_production_master.id');

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Grid filtering conditions
        $query->andFilterWhere([
            'project_production_master.id' => $this->id,
            'project_production_master.quotation_id' => $this->quotation_id,
            'project_production_master.client_id' => $this->client_id,
            'project_production_master.current_target_date' => $this->current_target_date,
        ]);

        $query->andFilterWhere(['like', 'project_production_master.name', $this->name])
                ->andFilterWhere(['like', 'project_production_master.project_production_code', $this->project_production_code])
                ->andFilterWhere(['like', 'project_production_master.fab_complete_percent', $this->fab_complete_percent])
                ->andFilterWhere(['like', 'project_production_master.elec_complete_percent', $this->elec_complete_percent])
                ->andFilterWhere(['like', 'clients.company_name', $this->clientName])
                ->andFilterWhere(['like', 'project_production_master.remark', $this->remark])
                ->andFilterWhere(['like', 'project_production_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterHaving(['like', 'production_fab_complete_percent', $this->production_fab_complete_percent])
                ->andFilterHaving(['like', 'production_elec_complete_percent', $this->production_elec_complete_percent])
//        ->andFilterHaving(['>=', 'ROUND((SUM(stock_outbound_details.dispatched_qty) * 100.0) / NULLIF(SUM(stock_outbound_details.qty), 0), 2)', $this->component_percentage]);
                ->andFilterHaving(['like', 'component_percentage', $this->component_percentage]);

        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');

        if ($type === 'overdue') {
            $query->andWhere(new \yii\db\Expression(
                                    "DATEDIFF(project_production_master.current_target_date, :today) < 0"
                    ))->addParams([':today' => $todayStr])
                    ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
                    ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
                    ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id]);
        }

        if ($type === 'neardue') {
            $query->andWhere(new \yii\db\Expression(
                                    "DATEDIFF(project_production_master.current_target_date, :today) BETWEEN 0 AND 5"
                    ))->addParams([':today' => $todayStr])
                    ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
                    ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
                    ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id]);
        }

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);
        $dataProvider->sort->attributes['client_id'] = [
            'asc' => ['clients.company_name' => SORT_ASC],
            'desc' => ['clients.company_name' => SORT_DESC],
        ];
        return $dataProvider;
    }
}
