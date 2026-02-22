<?php

namespace frontend\models\working\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\claim\ClaimsMaster;
use Yii;

/**
 * ClaimsMasterSearch represents the model behind the search form of `frontend\models\working\claim\ClaimsMaster`.
 */
class ClaimsMasterSearch extends ClaimsMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_master_id', 'claims_mi_id', 'updated_by'], 'integer'],
            [['claims_id', 'claim_type', 'claims_status', 'created_at', 'updated_at', 'claimant_id'], 'safe'],
            [['total_amount'], 'number'],
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
    public function search($params, $type = "", $claimMasterid = "") {
        $query = ClaimsMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case "personalSubmittedClaim":
                $query->where("claimant_id = " . Yii::$app->user->identity->id);
                break;
            case "viewClaimmasterDetail":
                $query->where("claims_id = '" . $claimMasterid . "'");
                break;
            case "hrTravelClaim":
                $query->where('claim_type="tra" AND claims_status<4');
                break;
            case "hrTravelClaimAll":
                $query->where('claim_type="tra"'); // AND claims_status<4');
                break;
            case "accountClaimPending":
                $query->join('INNER JOIN', 'master_incomings', 'master_incomings.id = claims_master.claims_mi_id AND master_incomings.current_step_task_id=7')
                        ->where('claim_type!="tra" AND claims_status=3'); // Show only after the document is received
                break;
            case "accountClaimAll":
                $query->where('claim_type!="tra"'); // AND claims_status<4');
                break;
            case "procClaimGRN":
                $query->join('LEFT JOIN', 'master_incomings', 'master_incomings.id = claims_master.claims_mi_id')
                        ->where('claim_type IN ("mat","pet") AND claims_status <= 3')
                        ->andWhere('(master_incomings.current_step <=3 OR master_incomings.current_step IS NULL)'); // Hide if already provide GRN in Doc Incoming
                break;
            case "waitingDocCount":
                $query->where('claims_status = 2 AND claimant_id = ' . Yii::$app->user->identity->id);
                break;
            case "general":
                break;
        }



        $query->join('INNER JOIN', 'user', 'claims_master.claimant_id = user.id')
                ->join('INNER JOIN', 'ref_claim_status', 'ref_claim_status.id = claims_master.claims_status')
                ->join('LEFT JOIN', 'master_incomings as aa', 'aa.id = claims_master.claims_mi_id');



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['claimant_id'] = [
            'asc' => ['user.fullname' => SORT_ASC],
            'desc' => ['user.fullname' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'claims_master_id' => $this->claims_master_id,
            'total_amount' => $this->total_amount,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'claims_id', $this->claims_id])
                ->andFilterWhere(['like', 'claim_type', $this->claim_type])
                ->andFilterWhere(['like', 'user.fullname', $this->claimant_id])
                ->andFilterWhere(['like', 'claims_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'aa.index_no', $this->claims_mi_id])
                ->andFilterWhere(['like', 'ref_claim_status.status_name', $this->claims_status]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['claims_master_id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
