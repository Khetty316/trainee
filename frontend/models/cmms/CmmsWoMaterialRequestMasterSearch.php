<?php

namespace frontend\models\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\cmms\CmmsWoMaterialRequestMaster;

/**
 * CmmsWoMaterialRequestMasterSearch represents the model behind the search form of `frontend\models\cmms\CmmsWoMaterialRequestMaster`.
 */
class CmmsWoMaterialRequestMasterSearch extends CmmsWoMaterialRequestMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'wo_id', 'finalized_status', 'fully_dispatched_status'], 'integer'],
            [['wo_type', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'safe'],
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
    public function search($params, $type) {
        $query = CmmsWoMaterialRequestMaster::find();

        $query->joinWith([
            'createdBy',
            'updatedBy',
        ]);

// add conditions that should always apply here
        switch ($type) {
            case "pending":
                $query->where(['in', 'cmms_wo_material_request_master.finalized_status', [1, 2]])
                ->andWhere(['in', 'cmms_wo_material_request_master.fully_dispatched_status', [0, 2]]);
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
            'wo_id' => $this->wo_id,
            'cmms_wo_material_request_master.finalized_status' => $this->finalized_status,
            'cmms_wo_material_request_master.fully_dispatched_status' => $this->fully_dispatched_status,
            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'wo_type', $this->wo_type])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        return $dataProvider;
    }
}
