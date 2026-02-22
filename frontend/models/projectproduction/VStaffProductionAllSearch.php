<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

class VStaffProductionAllSearch extends VStaffProductionAll {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_type', 'created_at', 'updated_at', 'assignee_fullname', 'assigner_fullname', 'assigned_start_date', 'assigned_complete_date', 'assigned_complete_date_individual', 'assigned_comments', 'deactivated_at', 'deactivated_by_fullname', 'task_name', 'panel_code', 'panel_description', 'panel_unit_code', 'panel_unit_name', 'panel_remark', 'panel_finalized_at', 'panel_elec_completed_at'], 'safe'],
            [['id', 'user_id', 'task_assign_elec_id', 'created_by', 'updated_by', 'taskAssignment_id', 'assigned_active_status', 'deactivated_by', 'taskToDo_id', 'task_sort', 'panel_id', 'panel_qty', 'panel_sort', 'panel_finalized_by', 'panel_elec_completed_by'], 'integer'],
            [['assigned_qty', 'assigned_complete_qty', 'assigned_complete_qty_individual', 'taskToDo_total_qty', 'taskToDo_assigned_qty', 'panel_elec_assign_percent', 'panel_elec_complete_percent'], 'number'],
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
    public function search($params, $type, $param1 = "") {
        $query = VStaffProductionAll::find();

        switch ($type) {
            case "myActiveTask":
                $query->where(['user_id' => Yii::$app->user->id, 'assigned_active_status' => 1, 'assigned_complete_date' => null]);
                break;
            case "myAllTask":
                $query->where(['user_id' => Yii::$app->user->id]);
                break;
            case "userOngoingTask":
                $query->where(['user_id' => $param1, 'assigned_active_status' => 1, 'assigned_complete_date' => null]);
                break;
        }
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
            'user_id' => $this->user_id,
            'task_assign_elec_id' => $this->task_assign_elec_id,
//            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'taskAssignment_id' => $this->taskAssignment_id,
            'assigned_qty' => $this->assigned_qty,
            'assigned_complete_qty' => $this->assigned_complete_qty,
            'assigned_complete_qty_individual' => $this->assigned_complete_qty_individual,
            'assigned_start_date' => \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->assigned_start_date),
            'assigned_complete_date' => \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->assigned_complete_date),
            'assigned_complete_date_individual' => \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->assigned_complete_date_individual),
            'assigned_active_status' => $this->assigned_active_status,
            'deactivated_at' => $this->deactivated_at,
            'deactivated_by' => $this->deactivated_by,
            'taskToDo_id' => $this->taskToDo_id,
            'taskToDo_total_qty' => $this->taskToDo_total_qty,
            'taskToDo_assigned_qty' => $this->taskToDo_assigned_qty,
            'task_sort' => $this->task_sort,
            'panel_id' => $this->panel_id,
            'panel_qty' => $this->panel_qty,
            'panel_sort' => $this->panel_sort,
            'panel_finalized_at' => $this->panel_finalized_at,
            'panel_finalized_by' => $this->panel_finalized_by,
            'panel_elec_assign_percent' => $this->panel_elec_assign_percent,
            'panel_elec_complete_percent' => $this->panel_elec_complete_percent,
            'panel_elec_completed_at' => $this->panel_elec_completed_at,
            'panel_elec_completed_by' => $this->panel_elec_completed_by,
            'task_type' => $this->task_type,
        ]);

        $query->andFilterWhere(['like', 'assignee_fullname', $this->assignee_fullname])
                ->andFilterWhere(['like', 'assigner_fullname', $this->assigner_fullname])
                ->andFilterWhere(['like', 'assigned_comments', $this->assigned_comments])
                ->andFilterWhere(['like', 'deactivated_by_fullname', $this->deactivated_by_fullname])
                ->andFilterWhere(['like', 'task_name', $this->task_name])
                ->andFilterWhere(['like', 'panel_code', $this->panel_code])
                ->andFilterWhere(['like', 'panel_description', $this->panel_description])
                ->andFilterWhere(['like', 'panel_unit_code', $this->panel_unit_code])
                ->andFilterWhere(['like', 'panel_unit_name', $this->panel_unit_name])
                ->andFilterWhere(['like', 'created_at', (\common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->created_at))])
                ->andFilterWhere(['like', 'panel_remark', $this->panel_remark]);

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        return $dataProvider;
    }
}
