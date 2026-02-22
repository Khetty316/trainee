<?php

namespace frontend\models\working\leavemgmt;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VMasterLeaveBreakdownSearch represents the model behind the search form of `frontend\models\working\leavemgmt\LeaveDetailBreakdown`.
 */
class MasterLeaveBreakdownSearch extends VMasterLeaveBreakdown {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'break_id', 'start_section', 'end_section', 'leave_confirm_year', 'leave_confirm_month', 'confirm_flag', 'is_recorded'], 'integer'],
            [['total_days'], 'double'],
            [['leave_code', 'requestor', 'leave_type_name', 'reason', 'start_date', 'end_date', 'created_at', 'hr_recall', 'hr_recall_by', 'hr_recall_at', 'hr_recall_remarks'], 'safe'],
            [['total_days', 'days_annual', 'days_unpaid', 'days_sick', 'days_others'], 'number'],
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
    public function search($params, $condition = '') {
        $query = VMasterLeaveBreakdown::find();

        // add conditions that should always apply here
        switch ($condition) {
            case "hrLeaveUnconfirm":
                $query->where("leave_status=4 AND confirm_flag=0");
                break;
            case "hrToRecord":
                $query->where("is_recorded=0");
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
        ]);

        $query
                ->andFilterWhere(['like', 'requestor', $this->requestor])
                ->andFilterWhere(['like', 'leave_code', $this->leave_code])
                ->andFilterWhere(['like', 'break_id', $this->break_id])
                ->andFilterWhere(['like', 'leave_type_name', $this->leave_type_name])
                ->andFilterWhere(['like', 'reason', $this->reason])
                ->andFilterWhere(['=', 'total_days', $this->total_days]);
        return $dataProvider;
    }

}
