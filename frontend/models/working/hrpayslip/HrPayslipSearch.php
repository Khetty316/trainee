<?php

namespace frontend\models\working\hrpayslip;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\hrpayslip\HrPayslip;

/**
 * HrPayslipSearch represents the model behind the search form of `frontend\models\working\hrpayslip\HrPayslip`.
 */
class HrPayslipSearch extends HrPayslip {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'pay_year', 'pay_month', 'created_by'], 'integer'],
            [['basic_salary', 'bonus', 'commission', 'director_fee', 'epf', 'socso', 'eis_sip', 'income_tax', 'unpaid_leave', 'annual_leave_pay', 'net_salary', 'employer_epf', 'employer_socso', 'employer_eis_sip'], 'number'],
            [['created_at'], 'safe'],
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
    public function search($params, $type = "", $param2 = []) {
        $query = HrPayslip::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'single':
                $query->where("user_id=" . $param2[0]);
                break;
            case 'generate-payslip':
                $query->where("pay_year=" . $param2[0] . " AND pay_month=" . $param2[1]);
                break;
        }


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
            'pay_year' => $this->pay_year,
            'pay_month' => $this->pay_month,
            'basic_salary' => $this->basic_salary,
            'bonus' => $this->bonus,
            'commission' => $this->commission,
            'director_fee' => $this->director_fee,
            'epf' => $this->epf,
            'socso' => $this->socso,
            'eis_sip' => $this->eis_sip,
            'income_tax' => $this->income_tax,
            'unpaid_leave' => $this->unpaid_leave,
            'annual_leave_pay' => $this->annual_leave_pay,
            'net_salary' => $this->net_salary,
            'employer_epf' => $this->employer_epf,
            'employer_socso' => $this->employer_socso,
            'employer_eis_sip' => $this->employer_eis_sip,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['pay_year' => SORT_DESC, 'pay_month' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
