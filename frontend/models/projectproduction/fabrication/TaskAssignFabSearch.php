<?php

namespace frontend\models\ProjectProduction\fabrication;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ProjectProduction\fabrication\TaskAssignFab;

/**
 * TaskAssignFabSearch represents the model behind the search form of `frontend\models\ProjectProduction\fabrication\TaskAssignFab`.
 */
class TaskAssignFabSearch extends TaskAssignFab {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'proj_prod_panel_id', 'prod_fab_task_id', 'active_sts'], 'integer'],
            [['quantity'], 'number'],
            [['current_target_date', 'start_date', 'complete_date', 'comments', 'created_at', 'updated_at', 'deactivated_at', 'taskCode', 'panelCode', 'assignee', 'created_by', 'updated_by', 'deactivated_by'], 'safe'],
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
    public function search($params, $params2 = "", $extraParams = [], $date = null) {
        $query = TaskAssignFab::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        switch ($params2) {
            case "viewAssignedTask":
                $query->where(['prod_fab_task_id' => $extraParams['taskId']]);
                break;
            case "indexFabInProgress":
                $query->where(['active_sts' => 1, 'task_assign_fab.complete_date' => null]);
                break;
        }
        // $model->projProdPanel->
        $query->select(['task_assign_fab.*',
            'GROUP_CONCAT(assignee.fullname SEPARATOR ";") as assignee',
            'projProdPanel.project_production_panel_code as panelCode']);
        $query->joinWith('projProdPanel as projProdPanel', false)
                ->joinWith('prodFabTask as prodFabTask', false)
                ->joinWith('taskAssignFabStaff as taskAssignFabStaff', false)->leftJoin('user as assignee', 'assignee.id=taskAssignFabStaff.user_id');
        $query->groupBy(['task_assign_fab.id']);
        $query->innerjoin('user as assigner', 'task_assign_fab.created_by=assigner.id');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'proj_prod_panel_id' => $this->proj_prod_panel_id,
            'prod_fab_task_id' => $this->prod_fab_task_id,
            'task_assign_fab.quantity' => $this->quantity,
            'prodFabTask.fab_task_code' => $this->taskCode,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'active_sts' => $this->active_sts,
            'current_target_date' => $this->current_target_date,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments])
                ->andFilterWhere(['like', 'projProdPanel.project_production_panel_code', $this->panelCode])
                ->andFilterWhere(['like', 'assigner.fullname', $this->created_by])
//                ->andFilterWhere(['like', 'DATE_FORMAT(task_assign_fab.start_date,\'%d/%m/%Y %H:%i\')', $this->start_date])
                ->andFilterWhere(['like', 'task_assign_fab.start_date', $this->start_date])
                ->andFilterWhere(['like', 'DATE_FORMAT(task_assign_fab.complete_date,\'%d/%m/%Y %H:%i\')', $this->complete_date])
//                ->andFilterWhere(['like', 'DATE_FORMAT(task_assign_fab.created_at,\'%d/%m/%Y %H:%i\')', $this->created_at])
                ->andFilterWhere(['like', 'task_assign_fab.created_at', $this->created_at])

        ;
        $query->having('assignee LIKE "%' . addslashes($this->assignee) . '%"');

        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');

        if ($date === 'overdue') {
            $query->andWhere(new \yii\db\Expression(
                                    "DATEDIFF(task_assign_fab.current_target_date, :today) < 0"
                    ))->addParams([':today' => $todayStr])
                    ->andWhere(['task_assign_fab.complete_date' => null]);
        }

        if ($date === 'neardue') {
            $query->andWhere(new \yii\db\Expression(
                                    "DATEDIFF(task_assign_fab.current_target_date, :today) BETWEEN 0 AND 5"
                    ))->addParams([':today' => $todayStr])
                    ->andWhere(['task_assign_fab.complete_date' => null]);
        }

        $dataProvider->sort->attributes['taskCode'] = [
            'asc' => ['prodFabTask.fab_task_code' => SORT_ASC],
            'desc' => ['prodFabTask.fab_task_code' => SORT_DESC]
        ];
        $dataProvider->sort->attributes['panelCode'] = [
            'asc' => ['panelCode' => SORT_ASC],
            'desc' => ['panelCode' => SORT_DESC]
        ];

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
