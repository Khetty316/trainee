<?php

namespace frontend\models\working\leavemgmt;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VMasterLeaveBreakdownSearch represents the model behind the search form of `frontend\models\working\leavemgmt\LeaveDetailBreakdown`.
 */
class LeaveMonthlySummarySearch extends LeaveMonthlySummaryTable {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'leave_id', 'start_section', 'end_section', 'leave_confirm_year', 'leave_confirm_month', 'confirm_flag'], 'integer'],
            [['start_date', 'end_date', 'created_at'], 'safe'],
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
        $query = LeaveMonthlySummary::find();




        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
////            return $dataProvider;
//        }

        // grid filtering conditions
//        $query->andFilterWhere([
//            'id' => $this->id,
//            'leave_id' => $this->leave_id,
//            'start_date' => $this->start_date,
//            'start_section' => $this->start_section,
//            'end_date' => $this->end_date,
//            'end_section' => $this->end_section,
//            'total_days' => $this->total_days,
//            'leave_confirm_year' => $this->leave_confirm_year,
//            'leave_confirm_month' => $this->leave_confirm_month,
//            'days_annual' => $this->days_annual,
//            'days_unpaid' => $this->days_unpaid,
//            'days_sick' => $this->days_sick,
//            'days_others' => $this->days_others,
//            'confirm_flag' => $this->confirm_flag,
//            'created_at' => $this->created_at,
//        ]);

        return $dataProvider;
    }

}
