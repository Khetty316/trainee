<?php

namespace frontend\models\cmms;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\cmms\CmmsPreventiveWorkOrderMaster;

/**
 * CmmsPreventiveWorkOrderMasterSearch represents the model behind the search form of `frontend\models\cmms\CmmsPreventiveWorkOrderMaster`.
 */
class CmmsPreventiveWorkOrderMasterSearch extends CmmsPreventiveWorkOrderMaster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_list_id', 'tool_list_id', 'id', 'active_sts', 'progress_status_id', 'assigned_by', 'cmms_asset_list_id', 'frequency_id'], 'integer'],
            [['commencement_date', 'next_date', 'start_time', 'end_time', 'remarks', 'created_at'], 'safe'],
            [['duration'], 'string', 'max' => 255]
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
        $query = CmmsPreventiveWorkOrderMaster::find();

        // add conditions that should always apply here
        switch ($context) {
            case "assignedTasks":
//                $query->where(['cmms_corrective_work_order_master.progress_status_id' => RefProgressStatus::$STATUS_ASSIGNED])
                      $query->where([
                          'exists',
                            RefAssignedPic::find()
                                ->select('id')
                                ->where(
                                    'ref_assigned_pic.preventive_work_order_master_id = cmms_preventive_work_order_master.id'
                                )
                                ->andWhere([
                                    'ref_assigned_pic.name' =>
                                        Yii::$app->user->identity->fullname
                                ])
                      ])
                      ->andWhere(['cmms_preventive_work_order_master.active_sts' => 1]);
                break;

//            case "superior":
//                $query->where([
//                    'exists',
//                    CmmsFaultList::find()
//                        ->select('id')
//                        ->where('cmms_fault_list.cmms_work_order_id = cmms_preventive_work_order_master.id')
//                        ->andWhere(['cmms_fault_list.superior_id' => Yii::$app->user->identity->id])
//                    ])
//                      ->andWhere(['cmms_preventive_work_order_master.active_sts' => 1]);
//                break;
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
            'next_date' => $this->next_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'active_sts' => $this->active_sts,
            'duration' => $this->duration,
            'progress_status_id' => $this->progress_status_id,
            'assigned_by' => $this->assigned_by,
            'cmms_asset_list_id' => $this->cmms_asset_list_id,
            'frequency_id' => $this->frequency_id,
            'created_at' => $this->created_at,
            'commencement_date' => $this->commencement_date
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
