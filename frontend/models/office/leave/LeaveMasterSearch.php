<?php

namespace frontend\models\office\leave;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;
use common\models\myTools\MyFormatter;

/**
 * LeaveMasterSearch represents the model behind the search form of `frontend\models\office\leave\LeaveMaster`.
 */
class LeaveMasterSearch extends VMasterLeave {

    public $monthYear;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'requestor_id', 'leave_type_code', 'superior_id', 'start_section', 'end_section', 'leave_status'], 'integer'],
            [['requestor_id', 'leave_type_code', 'superior_id', 'start_section', 'end_section', 'leave_status'], 'integer'],
            [['reason', 'start_date', 'end_date', 'created_at', 'monthYear'], 'safe'],
            [['total_days'], 'number'],
            [['requestor', 'requestor_email', 'leave_type_name', 'superior', 'superior_email', 'support_doc', 'relief'], 'string', 'max' => 255],
            [['start_sec_name', 'end_sec_name', 'leave_status_name', 'leave_code'], 'string', 'max' => 100],
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
        $query = VMasterLeave::find();

        // add conditions that should always apply here
        switch ($condition) {
            case "reliefApproval":
                $query->where(['leave_status' => 1, 'relief_user_id' => Yii::$app->user->identity->id]);
                break;
            case "superiorApproval":
                $query->where(['leave_status' => 2, 'superior_id' => Yii::$app->user->identity->id]);
                break;
            case "hrApproval":
                $query->where(" leave_status = 3");
                break;
//            case "directorApproval":
//                $query->where(" leave_status = 3");
//                break;
            case 'hrPending':
                $query->where(' leave_status < 4 ');
                break;
            case 'hrMonthlySummary':
                $query->where(' confirm_flag = 0 AND  leave_status >= 4 ');
                break;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        if ($this->leave_type_name === RefLeaveType::codeEmergency) {
            $query->andWhere(['emergency_leave' => 1]);
        } else {
            $query->andFilterWhere([
                'id' => $this->id,
                'total_days' => $this->total_days,
                'leave_status' => $this->leave_status
            ]);

            $query->andFilterWhere(['like', 'reason', $this->reason])
                    ->andFilterWhere(['like', 'leave_type_code', $this->leave_type_name])
                    ->andFilterWhere([
                        'or',
                        ['>', 'DATE(start_date)', $this->start_date],
                        ['=', 'DATE(start_date)', $this->start_date],
                        ['<', 'DATE(end_date)', $this->start_date],
                    ])
                    ->andFilterWhere([
                        'or',
                        ['<', 'DATE(end_date)', $this->end_date],
                        ['=', 'DATE(end_date)', $this->end_date],
                        ['>', 'DATE(start_date)', $this->end_date],
                    ])
                    ->andFilterWhere(['like', 'relief', $this->relief])
                    ->andFilterWhere(['like', 'superior', $this->superior])
                    ->andFilterWhere(['like', 'requestor', $this->requestor])
                    ->andFilterWhere(['like', 'leave_code', $this->leave_code])
            //this is for the combined date column
//                ->andFilterWhere(['or',
//                    ['like', 'DATE_FORMAT(start_date,\'%d/%m/%Y\')', $this->start_date],
//                    ['like', 'DATE_FORMAT(end_date,\'%d/%m/%Y\')', $this->start_date]])
            //this is for the monthYear column
//                ->andFilterWhere([
//                    'or',
//                    ['like', 'DATE_FORMAT(start_date,\'%M,%Y\')', $this->monthYear],
//                    ['like', 'DATE_FORMAT(end_date, "%M, %Y")', $this->monthYear],
//                ])
            ;
        }
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }
        return $dataProvider;
    }
}
