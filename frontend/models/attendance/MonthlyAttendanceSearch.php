<?php

namespace frontend\models\attendance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\attendance\MonthlyAttendance;

/**
 * MonthlyAttendanceSearch represents the model behind the search form of `frontend\models\attendance\MonthlyAttendance`.
 */
class MonthlyAttendanceSearch extends MonthlyAttendance {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'month', 'year', 'created_by', 'updated_by'], 'integer'],
            [['total_days', 'total_present', 'workday_present', 'unpaid_leave_present', 'rest_holiday_present', 'absent', 'leave_taken', 'late_in', 'early_out', 'miss_punch', 'short', 'sche', 'workday', 'workday_ot', 'holiday', 'holiday_ot', 'restday', 'restday_ot', 'unpaid_leave', 'unpaid_leave_ot'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
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
        $query = MonthlyAttendance::find();

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
            'month' => $this->month,
            'year' => $this->year,
            'total_days' => $this->total_days,
            'total_present' => $this->total_present,
            'workday_present' => $this->workday_present,
            'unpaid_leave_present' => $this->unpaid_leave_present,
            'rest_holiday_present' => $this->rest_holiday_present,
            'absent' => $this->absent,
            'leave_taken' => $this->leave_taken,
            'late_in' => $this->late_in,
            'early_out' => $this->early_out,
            'miss_punch' => $this->miss_punch,
            'short' => $this->short,
            'sche' => $this->sche,
            'workday' => $this->workday,
            'workday_ot' => $this->workday_ot,
            'holiday' => $this->holiday,
            'holiday_ot' => $this->holiday_ot,
            'restday' => $this->restday,
            'restday_ot' => $this->restday_ot,
            'unpaid_leave' => $this->unpaid_leave,
            'unpaid_leave_ot' => $this->unpaid_leave_ot,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        return $dataProvider;
    }

}
