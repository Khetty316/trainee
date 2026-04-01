<?php

namespace frontend\models\office\leave;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\leave\LeaveCompulsoryMaster;

/**
 * LeaveCompulsoryMasterSearch represents the model behind the search form of `frontend\models\office\leave\LeaveCompulsoryMaster`.
 */
class LeaveCompulsoryMasterSearch extends LeaveCompulsoryMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'days'], 'integer'],
            [['requestor', 'status', 'approval_by', 'start_date', 'end_date', 'requestor_remark', 'approval_remark', 'created_at'], 'safe'],
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
        $query = LeaveCompulsoryMaster::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "leave_compulsory_master.requestor = a.id");
        $query->join("LEFT JOIN", "user b", "leave_compulsory_master.approval_by = b.id");
        $query->join("LEFT JOIN", "user c", "leave_compulsory_master.updated_by = c.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days' => $this->days,
//            'requestor' => $this->requestor,
            'leave_compulsory_master.status' => $this->status,
//            'approval_by' => $this->approval_by,
//            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'a.fullname', $this->requestor])
                ->andFilterWhere(['like', 'b.fullname', $this->approval_by])
                ->andFilterWhere(['like', 'c.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'leave_compulsory_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'leave_compulsory_master.updated_at', $this->updated_at])
                ->andFilterWhere(['like', 'leave_compulsory_master.approved_at', $this->approved_at])
                ->andFilterWhere(['like', 'requestor_remark', $this->requestor_remark])
                ->andFilterWhere(['like', 'approval_remark', $this->approval_remark]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }

        return $dataProvider;
    }
}
