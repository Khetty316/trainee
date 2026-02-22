<?php

namespace frontend\models\cmms;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\myTools\MyFormatter;

/**
 * This is the model class for table "cmms_work_request".
 *
 * @property int $id
 * @property int|null $submitted_by
 * @property int|null $machine_breakdown_type_id
 * @property int|null $reviewed_by
 *
 * @property CmmsWorkOrderMaster[] $cmmsWorkOrderMasters
 * @property RefMachineBreakdownType $machineBreakdownType
 */
class CmmsCorrectiveWorkRequestSearch extends CmmsCorrectiveWorkRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'submitted_by', 'machine_breakdown_type_id', 'reviewed_by'], 'integer'],
            [['id'], 'unique'],
//            [['machine_breakdown_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMachineBreakdownType::className(), 'targetAttribute' => ['machine_breakdown_type_id' => 'id']],
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
        $query = CmmsCorrectiveWorkRequest::find();

        // add conditions that should always apply here
//        switch ($condition) {
//            case "reliefApproval":
//                $query->where(['leave_status' => 1, 'relief_user_id' => Yii::$app->user->identity->id]);
//                break;
//            case "superiorApproval":
//                $query->where(['leave_status' => 2, 'superior_id' => Yii::$app->user->identity->id]);
//                break;
//            case "hrApproval":
//                $query->where(" leave_status = 3");
//                break;
////            case "directorApproval":
////                $query->where(" leave_status = 3");
////                break;
//            case 'hrPending':
//                $query->where(' leave_status < 4 ');
//                break;
//            case 'hrMonthlySummary':
//                $query->where(' confirm_flag = 0 AND  leave_status >= 4 ');
//                break;
//        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
//        if ($this->leave_type_name === RefLeaveType::codeEmergency) {
//            $query->andWhere(['emergency_leave' => 1]);
//        } else {
//            $query->andFilterWhere([
//                'id' => $this->id,
//                'total_days' => $this->total_days,
//                'leave_status' => $this->leave_status
//            ]);
//
//            $query->andFilterWhere(['like', 'reason', $this->reason])
//                    ->andFilterWhere(['like', 'leave_type_code', $this->leave_type_name])
//                    ->andFilterWhere([
//                        'or',
//                        ['>', 'DATE(start_date)', $this->start_date],
//                        ['=', 'DATE(start_date)', $this->start_date],
//                        ['<', 'DATE(end_date)', $this->start_date],
//                    ])
//                    ->andFilterWhere([
//                        'or',
//                        ['<', 'DATE(end_date)', $this->end_date],
//                        ['=', 'DATE(end_date)', $this->end_date],
//                        ['>', 'DATE(start_date)', $this->end_date],
//                    ])
//                    ->andFilterWhere(['like', 'relief', $this->relief])
//                    ->andFilterWhere(['like', 'superior', $this->superior])
//                    ->andFilterWhere(['like', 'requestor', $this->requestor])
//                    ->andFilterWhere(['like', 'leave_code', $this->leave_code])
//            ;
//        }
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }
        return $dataProvider;
    }
}
