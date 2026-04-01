<?php

namespace frontend\models\cmms;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\cmms\CmmsCorrectiveWorkOrderMaster;

/**
 * CmmsCorrectiveWorkOrderMasterSearch represents the model behind the search form of `frontend\models\cmms\CmmsCorrectiveWorkOrderMaster`.
 */
class CmmsCorrectiveWorkOrderMasterSearch extends CmmsCorrectiveWorkOrderMaster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'machine_priority_id', 'active_sts', 'duration', 'cmms_fault_list_id', 'progress_status_id', 'assigned_by'], 'integer'],
            [['start_date', 'end_date', 'remarks'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params, $context)
    {
        $query = CmmsCorrectiveWorkOrderMaster::find();

        // add conditions that should always apply here
        switch ($context) {
            case "assignedTasks":
//                $query->where(['cmms_corrective_work_order_master.progress_status_id' => RefProgressStatus::$STATUS_ASSIGNED])
                      $query->where([
                          'exists',
                            RefAssignedPic::find()
                                ->select('id')
                                ->where(
                                    'ref_assigned_pic.corrective_work_order_master_id = cmms_corrective_work_order_master.id'
                                )
                                ->andWhere([
                                    'ref_assigned_pic.name' =>
                                        Yii::$app->user->identity->fullname
                                ])
                      ])
                      ->andWhere(['cmms_corrective_work_order_master.active_sts' => 1]);
                break;

            case "superior":
                $query->where([
                    'exists',
                    CmmsFaultList::find()
                        ->select('id')
                        ->where('cmms_fault_list.cmms_corrective_work_order_id = cmms_corrective_work_order_master.id')
                        ->andWhere(['cmms_fault_list.superior_id' => Yii::$app->user->identity->id])
                    ])
                      ->andWhere(['cmms_corrective_work_order_master.active_sts' => 1]);
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
            'machine_priority_id' => $this->machine_priority_id,
            'active_sts' => $this->active_sts,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration' => $this->duration,
            'cmms_fault_list_id' => $this->cmms_fault_list_id,
            'progress_status_id' => $this->progress_status_id,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
