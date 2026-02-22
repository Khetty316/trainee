<?php

namespace frontend\models\office\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\claim\ClaimMaster;

/**
 * ClaimMasterSearch represents the model behind the search form of `frontend\models\office\claim\ClaimMaster`.
 */
class ClaimMasterSearch extends ClaimMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['claim_code', 'claim_type', 'ref_code', 'created_at', 'updated_at', 'claimant_id', 'superior_id', 'claim_status', 'is_deleted', 'updated_by'], 'safe'],
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
    public function search($params, $type = "") {
        $query = ClaimMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case "pendingPersonal":
                $query->where(['IN', 'claim_master.claim_status', \frontend\models\RefGeneralStatus::STATUS_Pending])
                        ->andWhere(['claim_master.claimant_id' => \Yii::$app->user->identity->id])
                        ->andWhere(['claim_master.is_deleted' => 0]);
                break;

            case "allPersonal":
                $query->where(['claim_master.claimant_id' => \Yii::$app->user->identity->id]);
                break;

            case "pendingSuperior":
                $query->where(['claim_master.claim_status' => \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval]) 
                        ->andWhere(['claim_master.superior_id' => \Yii::$app->user->identity->id])
                        ->andWhere(['claim_master.is_deleted' => 0]);
                break;
            case "allSuperior":
                $query->where(['claim_master.superior_id' => \Yii::$app->user->identity->id]);
                break;
            
            case "pendingFinance":
                $query->where(['IN', 'claim_master.claim_status', \frontend\models\RefGeneralStatus::STATUS_Pending_Finance]) 
                        ->andWhere(['claim_master.is_deleted' => 0]);
                break;
            default:
                // Handle unknown type or no filter
//                $query->andWhere(['claim_master.is_deleted' => 0]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "claim_master.claimant_id = a.id");
        $query->join("LEFT JOIN", "user b", "claim_master.updated_by = b.id");
        $query->join("LEFT JOIN", "user c", "claim_master.superior_id = c.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'claimant_id' => $this->claimant_id,
//            'superior_id' => $this->superior_id,
            'total_amount' => $this->total_amount,
            'claim_status' => $this->claim_status,
            'is_deleted' => $this->is_deleted,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'a.fullname', $this->claimant_id])
                ->andFilterWhere(['like', 'b.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'c.fullname', $this->superior_id])
                ->andFilterWhere(['like', 'claim_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'claim_master.updated_at', $this->updated_at])
                ->andFilterWhere(['like', 'claim_code', $this->claim_code])
                ->andFilterWhere(['like', 'claim_type', $this->claim_type])
                ->andFilterWhere(['like', 'ref_code', $this->ref_code]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);
        
        return $dataProvider;
    }
}
